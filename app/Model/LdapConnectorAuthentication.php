<?php
App::uses('Router', 'Routing');

class LdapConnectorAuthentication extends AppModel {
	public $useTable = 'ldap_connector_authentication';

	public $actsAs = array(
		'Containable'
	);

	public $validate = array(
		'auth_users_id' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'LDAP Connector is required'
			)
		),
		'oauth_google_id' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'OAuth Google Connector is required'
			)
		),
		'saml_connector_id' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Saml Connector is required'
			)
		),
		'auth_awareness_id' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'LDAP Connector is required'
			)
		),
		/*'auth_policies_id' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'LDAP Connector is required'
			)
		),*/
	);

	public $belongsTo = array(
		'AuthUsers' => array(
			'className' => 'LdapConnector',
			'foreignKey' => 'auth_users_id'
		),
		'OauthGoogle' => array(
			'className' => 'OauthConnector',
			'foreignKey' => 'oauth_google_id'
		),
		'SamlConnector' => array(
			'className' => 'SamlConnector',
			'foreignKey' => 'saml_connector_id'
		),
		'AuthAwareness' => array(
			'className' => 'LdapConnector',
			'foreignKey' => 'auth_awareness_id'
		),
		'AuthPolicies' => array(
			'className' => 'LdapConnector',
			'foreignKey' => 'auth_policies_id'
		)
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Ldap Connector Authentication');
		$this->_group = parent::SECTION_GROUP_SYSTEM;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('Main')
			],
			'awareness' => [
				'label' => __('Awareness')
			],
			'policy' => [
				'label' => __('Policy')
			],
			'vendor_assessment' => [
				'label' => __('Online Assesments')
			],
			// 'third_party_audits' => [
			// 	'label' => __('Third Party Audits')
			// ],
			'account_reviews' => [
				'label' => __('Account Reviews')
			]
		];
		
		$this->fieldData = array(
			'general_auth' => [
				'label' => __('Use Default authentication'),
				'description' => __('OPTIONAL: If you click on the radiobox your system will start authenticating with local database. You need to create user accounts on the system (System / Settings / User Management).'),
				'renderHelper' => ['LdapConnectorAuthentications', 'generalAuthField'],
				'editable' => true
			],
			'auth_awareness' => [
				'label' => __('Enable LDAP for Awareness module authentication'),
				'description' => __('OPTIONAL: If you enable the checkbox the awareness portal (used for awareness trainings) will be active and authenticating users with LDAP. No local user accounts are needed for this functionality to operate. Portal URL: %s', Router::fullBaseUrl() . '/awareness'),
				'renderHelper' => ['LdapConnectorAuthentications', 'authAwarenessField'],
				'group' => 'awareness',
				'editable' => true,
				'type' => 'toggle'
			],
			'auth_policies' => [
				'label' => __('Enable Policy Portal'),
				'description' => __('OPTIONAL: If you enable the checkbox the policy portal (used for displaying policies on a dedicated portal) will be active and authenticating users with LDAP or not (simply allowing users without authentication). This functionality is tied with the settings you have defined for each policy (under Control Catalogue / Security Policies). Portal URL: %s', Router::fullBaseUrl() . '/policy'),
				'renderHelper' => ['LdapConnectorAuthentications', 'authPoliciesField'],
				'group' => 'policy',
				'editable' => true,
				'type' => 'toggle'
			],
			'auth_vendor_assessment' => [
				'label' => __('Enable Online Assessments Portal'),
				'description' => __('OPTIONAL: If you enable the checkbox the online assessments portal will be active and also re-use general eramba authentication configuration. Portal URL: %s', Router::fullBaseUrl() . '/portal/vendor-assessments'),
				'renderHelper' => ['LdapConnectorAuthentications', 'authVendorAssessmentField'],
				'group' => 'vendor_assessment',
				'editable' => true,
				'type' => 'toggle'
			],
			// 'auth_compliance_audit' => [
			// 	'label' => __('Enable Third Party Audit Portal'),
			// 	'description' => __('OPTIONAL: If you enable the checkbox the third party audits portal will be active and also re-use general eramba authentication configuration.'),
			// 	'renderHelper' => ['LdapConnectorAuthentications', 'authComplianceAuditField'],
			// 	'group' => 'third_party_audits',
			// 	'editable' => true,
			// 	'type' => 'toggle'
			// ],
			'auth_account_review' => [
				'label' => __('Enable Account Reviews Portal'),
				'description' => __('OPTIONAL: If you enable the checkbox the account reviews portal will be active and also re-use general eramba authentication configuration. Portal URL: %s', Router::fullBaseUrl() . '/portal/account-reviews'),
				'renderHelper' => ['LdapConnectorAuthentications', 'authAccountReviewField'],
				'group' => 'account_reviews',
				'editable' => true,
				'type' => 'toggle'
			],
		);

		parent::__construct($id, $table, $ds);
	}

	const GENERAL_AUTH_DEFAULT = 1;
	const GENERAL_AUTH_LDAP = 2;
	const GENERAL_AUTH_OAUTH_GOOGLE = 3;
	const GENERAL_AUTH_SAML = 4;

	const VALUE_ENABLED = 1;
	const VALUE_DISABLED = 0;

	public function getRecordTitle($id = null) {
		return __('Authentication');
	}

	/**
	 * Generic method that returns LDAP auth information. Supports overwriting with local configuration.
	 *
	 * @todo possibility to define in local configuration, whole ldap auth.
	 * @todo use authconnector.
	 */
	public function getAuthData()
	{
		if (($data = Cache::read('ldap_auth_data', 'ldap')) === false) {
			$data = $this->find('first', array(
				'recursive' => 0
			));	
			
			$data = $this->attributesToLowercase($data);

			// Add OAuth data
			$data['OauthGoogle'] = $data['OauthGoogle'];

			// Add SAML data
			$data['SamlConnector'] = $data['SamlConnector'];

			Cache::write('ldap_auth_data', $data, 'ldap');
		}
		
		return $data;
	}

	public function afterSave($created, $options = array()) {
		Cache::clearGroup('ldap', 'ldap');
	}

	public function afterFind($results, $primary = false)
	{
		if (!empty($results[0])) {
			$results[0] = $this->initGeneralAuth($results[0]);
		}
		
		return $results;
	}

	public function beforeValidate($options = array()) {
		$this->data = $this->initGeneralAuth($this->data);
		$this->handleValidation();

		return true;
	}

	private function initGeneralAuth($data)
	{
		reset($data);
		$key = key($data);

		$generalAuth = self::GENERAL_AUTH_DEFAULT;
		$authUsers = 0;
		$oauthGoogle = 0;
		$authSaml = 0;
		if (isset($data[$key]['general_auth'])) {
			$generalAuth = $data[$key]['general_auth'];

			switch ($generalAuth) {
				case self::GENERAL_AUTH_LDAP:
					$authUsers = 1;
					$oauthGoogle = 0;
					$authSaml = 0;
					break;
				case self::GENERAL_AUTH_OAUTH_GOOGLE:
					$authUsers = 0;
					$oauthGoogle = 1;
					$authSaml = 0;
					break;
				case self::GENERAL_AUTH_SAML:
					$authUsers = 0;
					$oauthGoogle = 0;
					$authSaml = 1;
					break;
			}
		} elseif (isset($data[$key]['auth_users']) && isset($data[$key]['oauth_google']) && isset($data[$key]['auth_saml'])) {
			if ($data[$key]['auth_users'] == 1) { // LDAP is selected
				$generalAuth = self::GENERAL_AUTH_LDAP;
				$authUsers = 1;
				$oauthGoogle = 0;
				$authSaml = 0;
			} elseif ($data[$key]['oauth_google'] == 1) { // OAuth Google is selected
				$generalAuth = self::GENERAL_AUTH_OAUTH_GOOGLE;
				$authUsers = 0;
				$oauthGoogle = 1;
				$authSaml = 0;
			} elseif ($data[$key]['auth_saml'] == 1) { // Saml is selected
				$generalAuth = self::GENERAL_AUTH_SAML;
				$authUsers = 0;
				$oauthGoogle = 0;
				$authSaml = 1;
			} // If neither one condition is true: Default auth is selected
		} else {
			return $data;
		}

		$data[$key]['general_auth'] = $generalAuth;
		$data[$key]['auth_users'] = $authUsers;
		$data[$key]['oauth_google'] = $oauthGoogle;
		$data[$key]['auth_saml'] = $authSaml;

		return $data;
	}

	private function handleValidation() {
		if (empty($this->data['LdapConnectorAuthentication']['auth_users'])) {
			$this->validator()->remove('auth_users_id');
		}

		if (empty($this->data['LdapConnectorAuthentication']['oauth_google'])) {
			$this->validator()->remove('oauth_google_id');
		}

		if (empty($this->data['LdapConnectorAuthentication']['auth_saml'])) {
			$this->validator()->remove('saml_connector_id');
		}

		if (empty($this->data['LdapConnectorAuthentication']['auth_awareness'])) {
			$this->validator()->remove('auth_awareness_id');
		}

		if (empty($this->data['LdapConnectorAuthentication']['auth_policies'])) {
			$this->validator()->remove('auth_policies_id');
		}
	}

	public function attributesToLowercase($authentication) {
		$authentication['AuthUsers'] = $this->AuthUsers->attributesToLowercase($authentication['AuthUsers']);
		$authentication['AuthAwareness'] = $this->AuthAwareness->attributesToLowercase($authentication['AuthAwareness']);
		$authentication['AuthPolicies'] = $this->AuthPolicies->attributesToLowercase($authentication['AuthPolicies']);

		return $authentication;
	}

	/**
	 * Change authentication setting.
	 * @param  string $setting Setting field name.
	 * @param  int $value Setting value.
	 * @return boolean Success.
	 */
	public function changeValue($setting, $value) {
		$data = [
			'id' => 1,
			$setting => $value
		];

		return $this->save($data, ['fieldList' => [$setting], 'validate' => false]);
	}
}
