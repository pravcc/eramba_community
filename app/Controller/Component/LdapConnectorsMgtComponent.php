<?php
App::uses('Component', 'Controller');
App::uses('Ldap', 'Lib/Ldap');
App::uses('GroupConnector', 'Lib/Ldap');
App::uses('AuthConnector', 'Lib/Ldap');

class LdapConnectorsMgtComponent extends Component {
	private $ldapConnection;
	protected $_bindDn;
	protected $_bindPw;
	public $ldapError = false;

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function ldapConnect($url, $port, $bindDn, $bindPw) {
		$this->_bindDn = $bindDn;
		$this->_bindPw = $bindPw;
		try {
			$ldapConnection = ldap_connect($url, $port) or die("Could not connect to $ldaphost");

			ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3) or die("Could not set ldap protocol");
			ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0) or die("Could not set ldap protocol");
			ldap_set_option($ldapConnection, LDAP_OPT_NETWORK_TIMEOUT, 10) or die("Could not set ldap protocol");
			// ldap_set_option($ldapConnection, LDAP_OPT_SIZELIMIT, 100) or die("Could not set ldap protocol");


			if (!$ldapConnection) {
				throw new CakeException("Could not connect to LDAP authentication server");
			}

			$bind = $this->bindLdap($ldapConnection);
			// $bind = @ldap_bind($ldapConnection, $bindDn, $bindPw);

			if (!$bind) {
				throw new CakeException("Could not bind to LDAP authentication server - check your bind DN and password.");
			}

			$this->ldap = $ldapConnection;
		}
		catch (Exception $e) {
			$errorMsg = 'Error occured: ' . $e->getMessage();

			$this->ldapError = $errorMsg;
			return $errorMsg . "\n";
		}
		
		return true;
	}

	protected function bindLdap($ldapConnection) {
		return @ldap_bind($ldapConnection, $this->_bindDn, $this->_bindPw);
	}

	/**
	 * Get a connector class based on array or id. 
	 *
	 * @deprecated in favor of LdapConnector::getConnector()
	 */
	public function getConnector($connector) {
		if (!is_array($connector)) {
			$data = ClassRegistry::init('LdapConnector')->find('first', array(
				'conditions' => array(
					'LdapConnector.id' => $connector
				),
				'recursive' => -1
			));

			if (empty($data)) {
				throw new NotFoundException();
			}

			$connector = $data['LdapConnector'];
		}

		$LdapConnector = null;
		if ($connector['type'] == LDAP_CONNECTOR_TYPE_GROUP) {
			$LdapConnector = new GroupConnector($connector);
		}
		else {
			$LdapConnector = new AuthConnector($connector);
		}

		return $LdapConnector;
	}

	/**
	 * @deprecated
	 */
	public function getData2($baseDn, $filter, $attributes) {
		$results = ldap_search($this->ldap, $baseDn, $filter, array($attributes));
		$results = ldap_get_entries($this->ldap, $results);

		debug($results);
	}

	public function getData($data, $options = array()) {
		if (empty($this->ldap)) {
			return false;
		}

		$LdapConnector = $this->getConnector($data);
		$LdapConnector->_ldap = $this->ldap;

		if ($data['type'] == 'authenticator') {
			
			$results = $LdapConnector->getUser($data['_ldap_auth_filter_username_value']);

			$results = $this->cleanUpEntry($results);
		}
		elseif ($data['type'] == 'group') {
			
			if ($options['testType'] == 'listUsers') {
				$members = $LdapConnector->getMembersOfGroup($options['groupName']);

				$results[] = $LdapConnector->getLastSearch();

				$cleanMembers = $this->cleanUpEntry($members);
				if ($LdapConnector->isEmailValueBuilt()) {
					$i = 0;
					foreach ($cleanMembers as &$m) {
						$m['__email'] = $LdapConnector->buildEmailValue($members[$i]);

						$i++;
					}
				}

				$results[] = $cleanMembers;
			}

			if ($options['testType'] == 'listGroups') {
				$groups = $LdapConnector->getAllGroups();

				$results[] = $LdapConnector->getLastSearch();
				$results[] = $this->cleanUpEntry($groups);
			}
		}

		return $results;
	}

	/**
	 * Get all users related to a connector.
	 *
	 * @deprecated
	 */
	public function getConnectorUsers($connector) {
		$authAttr = $connector['ldap_auth_attribute'];

		$filter = '(| (' . $authAttr . '=*) )';
		$results = ldap_search($this->ldap, $connector['ldap_base_dn'], $filter, array($authAttr));
		$results = $this->cleanUpEntry(ldap_get_entries($this->ldap, $results));

		$users = array();
		foreach ($results as $result) {
			$users[] = $result[$authAttr];
		}

		return $users;
	}

	/**
	 * Search through users related to a connector.
	 *
	 * @deprecated
	 */
	public function searchUsers($connector, $keyword = null) {
		$authAttr = $connector['ldap_auth_attribute'];

		if (empty($keyword)) {
			return false;
		}

		$attrVal = '*' . $keyword . '*';

		$filter = '(| (' . $authAttr . '=' . $attrVal . ') )';
		$results = ldap_search($this->ldap, $connector['ldap_base_dn'], $filter, array($authAttr));
		$results = $this->cleanUpEntry(ldap_get_entries($this->ldap, $results));

		$users = array();
		foreach ($results as $result) {
			$users[] = $result[$authAttr];
		}

		return $users;
	}

	/**
	 * @deprecated
	 */
	public function getGroups($data) {
		if (empty($this->ldap)) {
			return false;
		}

		if ($data['type'] == 'group') {
			$results = array();
			$attributes = array();

			$LdapConnector = new GroupConnector($data);
			$LdapConnector->_ldap = $this->ldap;

			$groups = $LdapConnector->getAllGroups();
			$listArray = $LdapConnector->formatGroups($groups);

			return $listArray;

			// $results = $this->cleanUpEntry($groups);
			// debug($results);exit;

			/*if (!empty($data['ldap_grouplist_filter'])) {
				$filter = $data['ldap_grouplist_filter'];

				if (!empty($data['ldap_grouplist_name'])) {
					$attributes[] = $data['ldap_grouplist_name'];
				}
				// debug($filter);
				// debug($attributes);
				$entries = $this->makeSearch($data['ldap_base_dn'], $filter, $attributes);
				$results = $this->cleanUpEntry($entries);
				// debug($data);
				// debug($results);exit;
				// exit;
				// $search = ldap_search($this->ldap, $data['ldap_base_dn'], $filter, $attributes);
				// debug(ldap_count_entries($this->ldap, $search));
				// $results = $this->cleanUpEntry(ldap_get_entries($this->ldap, $search));
				if (!empty($results)) {
					$groups = array();
					foreach ($results as $result) {
						$val = $result[mb_strtolower($data['ldap_grouplist_name'])];
						$groups[$val] = $val;
					}
					// debug($groups);exit;
					return $groups;
				}

				return false;
			}*/
		}

		return false;
	}

	/**
	 * @deprecated
	 */
	public function getUserEmailsByGroups($data, $groups, $policyAuth) {
		if (empty($this->ldap)) {
			return false;
		}

		if ($data['type'] == 'group') {
			$LdapConnector = $this->getConnector($data);
			$LdapConnector->_ldap = $this->ldap;

			return $LdapConnector->getGrouppedEmailsList($groups);

			/*$results = array();
			foreach ($groups as $group) {
				$members = $LdapConnector->getMembersOfGroup($group);
				array_shift($members);
				
				foreach ($members as $member) {
					$email = $LdapConnector->buildEmailValue($member);
					$results[$email][] = $group;
				}
			}

			return $results;
			

			$results = array();
			$attributes = array();

			//if (!empty($data['ldap_groupmemberlist_filter'])) {
				//$filter = $data['ldap_groupmemberlist_filter'];

				$filters = array();
				foreach ($groups as $group) {
					$filters[] = sprintf('(%s=%s)', $data['ldap_grouplist_name'], $group);
				}

				$filter = '(| ' . (implode(' ', $filters)) . ' )';

				if (!empty($data['ldap_groupmemberlist_name'])) {
					$attributes[] = $data['ldap_groupmemberlist_name'];
				}

				$search = ldap_search($this->ldap, $data['ldap_base_dn'], $filter, $attributes);
				$results = $this->cleanUpEntry(ldap_get_entries($this->ldap, $search));
				// debug($attributes);
				// exit;
				if (!empty($results)) {
					$emails = array();
					foreach ($results as $groupValue => $result) {
						$group = $this->getCN($groupValue);

						$user = $result[$data['ldap_groupmemberlist_name']];
						if (is_array($user)) {
							foreach ($user as $item) {
								if (!empty($policyAuth['ldap_email_attribute'])) {
									$emails = $this->addEmailItem($policyAuth, $item, $group, $emails);
								}
								elseif (!empty($policyAuth['domain'])) {
									$emails = $this->addEmail($item, $group, $emails, $policyAuth['domain']);
								}
							}
						}
						else {
							if (!empty($policyAuth['ldap_email_attribute'])) {
								$emails = $this->addEmailItem($policyAuth, $user, $group, $emails);
							}
							elseif (!empty($policyAuth['domain'])) {
								$emails = $this->addEmail($user, $group, $emails, $policyAuth['domain']);
								//$emails[] = $user . '@' . $policyAuth['domain'];
							}
						}
					}
					
					return ($emails);
				}

				return false;
			//}*/
		}

		return false;
	}

	/**
	 * @deprecated
	 */
	private function addEmail($user, $group, $emails, $domain) {
		if (empty($domain)) {
			return $emails;
		}

		$userVal = $this->getCN($user) . '@' . $domain;

		if (!isset($emails[$userVal])) {
			$emails[$userVal] = array($group);
		}
		else {
			$emails[$userVal][] = $group;
		}

		return $emails;
	}

	/**
	 * @deprecated
	 */
	private function addEmailItem($policyAuth, $user, $group, $emails) {
		$filters = array();
		$explode = explode(',', $user);
		foreach ($explode as $entry) {
			$explode2 = explode('=', $entry);
			if ($explode2[0] == $policyAuth['ldap_auth_attribute']) {
				$filters[] = sprintf('(%s)', $entry);
			}
		}

		$filter = '(| ' . (implode(' ', $filters)) . ' )';

		$search = ldap_search($this->ldap, $policyAuth['ldap_base_dn'], $filter, array($policyAuth['ldap_email_attribute']));
		$results = $this->cleanUpEntry(ldap_get_entries($this->ldap, $search));
	
		if (!isset($results[$user][$policyAuth['ldap_email_attribute']])) {
			return $this->addEmail($user, $group, $emails, $policyAuth['domain']);
		}

		$emailVal = $results[$user][$policyAuth['ldap_email_attribute']];

		if (!isset($emails[$emailVal])) {
			$emails[$emailVal] = array($group);
		}
		else {
			$emails[$emailVal][] = $group;
		}

		return $emails;
	}

	/**
	 * @deprecated in favor of GroupConnector::getMemberList()
	 */
	public function getUsersByGroups($data, $groups) {
		if (empty($this->ldap)) {
			return false;
		}

		if ($data['type'] == 'group') {
			$LdapConnector = $this->getConnector($data);
			$LdapConnector->_ldap = $this->ldap;

			$results = array();
			foreach ($groups as $group) {
				$members = $LdapConnector->getMembersOfGroup($group, array(
					'fetchEmail' => false
				));

				array_shift($members);
				
				foreach ($members as $member) {
					$name = $LdapConnector->getMemberName($member);
					$results[$name] = $name;
				}
			}

			return $results;
		}

		return false;
	}

	/**
	 * @deprecated
	 */
	private function addUserItem($user, $group, $users) {
		$userVal = $this->getCN($user);

		if (!isset($users[$userVal])) {
			$users[$userVal] = array($group);
		}
		else {
			$users[$userVal][] = $group;
		}

		return $users;
	}

	/**
	 * Extract CN from DN.
	 * @param  string $dn DN.
	 * @return string     CN.
	 */
	private function getCN( $dn ) {
		preg_match( '/[^,]*/', $dn, $matchs, PREG_OFFSET_CAPTURE, 3 );
		if ( ! empty( $matchs ) ) {
			return $matchs[0][0];
		}

		return false;
	}

	/**
	 * Clean LDAP results returned from ldap_get_entries function.
	 */
	private function cleanUpEntry($entry) {
		$retEntry = array();
		for ( $i = 0; $i < $entry['count']; $i++ ) {
			if (is_array($entry[$i])) {
				$subtree = $entry[$i];

				//This condition should be superfluous so just take the recursive call
				//adapted to your situation in order to increase perf.
				if ( ! empty($subtree['dn']) and ! isset($retEntry[$subtree['dn']])) {
					$retEntry[$subtree['dn']] = $this->cleanUpEntry($subtree);
				}
				else {
					$retEntry[] = $this->cleanUpEntry($subtree);
				}
			}
			else {
				$attribute = $entry[$i];
				if ( $entry[$attribute]['count'] == 1 ) {
					$retEntry[$attribute] = $entry[$attribute][0];
				}
				else {
					for ( $j = 0; $j < $entry[$attribute]['count']; $j++ ) {
						$retEntry[$attribute][] = $entry[$attribute][$j];
					}
				}
			}
		}
		return $retEntry;
	}
}
