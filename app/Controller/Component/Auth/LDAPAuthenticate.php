<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('AuthConnector', 'Lib/Ldap');

class LDAPAuthenticate extends BaseAuthenticate {
	public $components = array('Auth');

	protected $_mapErrors = [];
	protected $_customError = false;

	public function __construct(ComponentCollection $collection, $settings) {
		parent::__construct($collection, $settings);

		$this->_mapErrors = [
			773 => __('Your LDAP password is expired')
		];
	}

	protected function _setCustomError($extendedError)
	{
		$code = $this->parseExentedLdapErrorCode($extendedError);

		if (isset($this->_mapErrors[$code])) {
			$this->_customError = $this->_mapErrors[$code];
		}
	}

	public function getCustomError()
	{
		return $this->_customError;
	}

	/**
	* Helper function to connect to the LDAP server
	* Looks at the plugin's settings to get the LDAP connection details
	* 
	* @return LDAP connection as per ldap_connect(), false on failure
	*/
	private function __ldapConnect($AuthConnector) {
		// temporarily handle LDAP port this way
		$port = $AuthConnector->config('port');
		if (!is_numeric($port)) {
			$port = 389;
		}

		$ldapConnection = ldap_connect($this->settings['ldap_url'], $port);

		ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3) or die("Could not set ldap protocol");
		ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0) or die("Could not set ldap protocol");
		ldap_set_option($ldapConnection, LDAP_OPT_NETWORK_TIMEOUT, 10) or die("Could not set ldap protocol");

		if (!$ldapConnection) {
			return false;
		}

		// no way around than suppressing the error output
		$bind = @ldap_bind($ldapConnection, $this->settings['ldap_bind_dn'], $this->settings['ldap_bind_pw']);

		if (!$bind) {
			return false;
		}

		return $ldapConnection;
	}

	/**
	 * Authentication hook to authenticate a user against an LDAP server.
	 * @param CakeRequest $request The request that contains login information.
	 * @param CakeResponse $response Unused response object.
	 * @return mixed. False on login failure. An array of User data on success.
	 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {

		//Configure::write('debug', 0);
		// This will probably be cn or an email field to search
		/// $this->log("[LDAPAuthCake.authenticate] Authentication started", 'ldapauth');
		$fields = $this->settings['form_fields'];

		$userField = $fields['username'];
		$passField = $fields['password'];

		$userModel = $this->settings['userModel'];
		$model = ClassRegistry::init($userModel);

		// Definitely not authenticated if we haven't got the request data...
		if (!isset($request->data[ $userModel ])) {
			///	$this->log("[LDAPAuthCake.authenticate] No request data, cannot authenticate", 'ldapauth');
			return false;
		}

		// We need to know the username, or email, or some other unique ID
		$submittedDetails = $request->data[ $userModel ];

		if (!isset($submittedDetails[$userField])) {
			///	$this->log("[LDAPAuthCake.authenticate] No username supplied, cannot authenticate", 'ldapauth');
			return false;
		}

		// Make sure it's a valid string...
		$username = $submittedDetails[$userField];
		if (!is_string($username)) {
			///	$this->log("[LDAPAuthCake.authenticate] Invalid username, cannot authenticate", 'ldapauth');
			return false;
		}

		// Make sure they gave us a password too...
		$password = $submittedDetails[$passField];
		if (!is_string($password) || empty($password)) {
			return false;
		}

		// Get the ldap_filter setting and insert the username
		$AuthConnector = new AuthConnector($this->settings['LdapConnector']);
		// $ldapConnection = $this->ldapConnection = $AuthConnector->connect();
		$ldapFilter = $AuthConnector->getUserFilter($username);

		// Connect to LDAP server and search for the user object
		$ldapConnection = $this->ldapConnection = $this->__ldapConnect($AuthConnector);
		if ($ldapConnection === false) {
			$model->loginErrorMsg = __('We had issues connecting to your LDAP system, login with your admin credentials and review your settings');
			
			// possible to set flash through AuthComponent
			// $this->_Collection->Auth->flash($message);

			return false;
		}

		$attributes = array($this->settings['ldap_attribute']);
		if (isset($this->settings['ldap_memberof_attribute']) && !empty($this->settings['ldap_memberof_attribute'])) {
			$attributes[] = $this->settings['ldap_memberof_attribute'];
		}

		// Suppress warning when no object found
		$results = ldap_search($ldapConnection, $this->settings['ldap_base_dn'], $ldapFilter, $attributes, 0, 1);

		// Failed to find user details, not authenticated.
		if (!$results || ldap_count_entries($ldapConnection, $results) == 0) {
			//	$this->log("[LDAPAuthCake.authenticate] Could not find user $username", 'ldapauth');
			return false;
		}

		// Got multiple results, sysadmin did something wrong!
		if (ldap_count_entries($ldapConnection, $results) > 1) {
			///	$this->log("[LDAPAuthCake.authenticate] Multiple LDAP results for $username", 'ldapauth');
			return false;
		}

		// Found the user! Get their details
		$ldapUser = ldap_get_entries($ldapConnection, $results);

		$ldapUser = $ldapUser[0];

		$results = array();

		// Now try to re-bind as that user
		$bind = @ldap_bind($ldapConnection, $ldapUser['dn'], $password);
		
		// If the password didn't work, bomb out
		if (!$bind) {
			if (ldap_get_option($ldapConnection, 0x0032, $extendedError)) {
				$this->_setCustomError($extendedError);
			}

			return false;
		}

		// lets reuse AuthConnector class having temporary config just to be able to get correct Auth Attribute value to respect case-sensitive chars
		// @todo will be refactored completely to use connector classes
		// $AuthConnector = new AuthConnector(array('ldap_auth_attribute' => $this->settings['ldap_attribute']));
		$remoteUsernameValue = $AuthConnector->getAttribute($ldapUser, 'ldap_auth_attribute');

		$conds = array(
			$userModel . '.login' => $remoteUsernameValue
		);

		if ($this->settings['loginType'] == 'eramba') {
			$conds[$userModel . '.local_account'] = 0;
		}

		$dbUser = $model->find('first', array(
			'conditions' => $conds,
			'recursive'	=> 1
		));

		// lets adjust login stored in database to match exactly to the one in LDAP in case its already stored incorrectly
		if (!empty($dbUser) && $this->settings['loginType'] == 'awareness') {
			if ($dbUser[$userModel][$fields['username']] !== $remoteUsernameValue) {
				$model->id = $dbUser[$userModel][$model->primaryKey];
				$ret = $model->saveField('login', $remoteUsernameValue);
			}
		}

		if (empty($dbUser)) {
			///	$this->log("[LDAPAuthCake.authenticate] Could not find a database entry for $username", 'ldapauth');

			if ( $userModel == 'User' ) {
				//we dont want to create user automatically for eramba app
				return false;
			}
			
			// Saving user is specific for Awareness module.
			$results['login'] = $username;

			if (!$model->save($results)) {
				///	echo "Failed to save new user\n"; print_r($results); print_r($username);
				return false;
			}

			$id = $model->id;
			$dbUser = $model->find('first', array(
				'conditions' => array(
					'id' => $id
				),
				'recursive' => 1
			));
		}

		// Ensure there's nothing in the password field
		unset($dbUser[$userModel][$fields['password']]);

		if ($userModel == 'User') {
			$dbUser[$userModel]['Group'] = $dbUser['Group'];
		}

		if (!empty($this->settings['ldap_memberof_attribute'])) {
			$dbUser[$userModel]['ldapGroup'] = self::getGroups($ldapUser, $this->settings['ldap_memberof_attribute']);
		}
		
		// ...and return the user object.
		return $dbUser[$userModel];
	}

	public function parseExentedLdapErrorCode($message) {
	    $code = null;
	    if (preg_match("/(?<=data\s).*?(?=\,)/", $message, $code)) {
	        return $code[0];
	    }
	    return null;
	}

	public static function getUserGroupsList($ldapUser, $AuthConnector) {
		return self::getGroups($AuthConnector->getUser($ldapUser)[0], $AuthConnector->config('ldap_memberof_attribute'));
	}

	private static function getGroups($ldapUser, $attr = 'memberof') {
		if (!isset($ldapUser[$attr]) || $ldapUser[$attr]['count'] == 0) {
			return false;
		}

		$groups = array();
		for ($i = 0; $i < $ldapUser[$attr]['count']; $i++) {
			$groups[] = $ldapUser[$attr][$i];
		}

		return $groups;
	}

	/**
	 * Extract CN from DN.
	 * @param  string $dn DN.
	 * @return string     CN.
	 * @deprecated e1.0.6.039 This hardcodedly trimmed required parts of a group (memberOf) values pulled via LDAP.
	 */
	private function getCN( $dn ) {
		preg_match( '/[^,]*/', $dn, $matchs, PREG_OFFSET_CAPTURE, 3 );
		if ( ! empty( $matchs ) ) {
			return $matchs[0][0];
		}

		return false;
	}

}
