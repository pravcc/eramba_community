<?php
App::uses('GroupConnector', 'Lib/Ldap');
App::uses('AuthConnector', 'Lib/Ldap');

class LdapConnector extends AppModel {
	public $displayField = 'name';
	
	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'workflow' => false
	);

	public $actsAs = array(
		'FieldData.FieldData',
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description', 'host', 'domain', 'port', 'type', 'status'
			)
		),
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = [
		'name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Name is required'
		],
		'host' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Host is required'
			],
			'url' => [
				'rule' => 'urlCustom',
				'message' => 'Please enter a valid URL'
			]
		],
		'domain' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Domain is required'
			],
			'url' => [
				'rule' => 'url',
				'message' => 'Please enter a valid domain'
			]
		],
		'port' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Port is required'
			],
			'numeric' => [
				'rule' => 'numeric',
				'message' => 'Port must be numeric'
			],
			'range' => [
				'rule' => ['range', -1, 65536],
				'message' => 'Port must be a number within 0 - 65535'
			],
		],
		'ldap_bind_dn' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'LDAP username is required'
			]
		],
		'ldap_bind_pw' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'LDAP password is required'
			]
		],
		'ldap_base_dn' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'LDAP Base DN is required'
			]
		]
	];

	public $hasMany = array(
		'AwarenessProgram',
		'SecurityPolicy',
	);

	public $hasOne = array(
		'LdapAuthUsers' => array(
			'className' => 'LdapConnectorAuthentication',
			'foreignKey' => 'auth_users_id'
		),
		'LdapAuthAwareness' => array(
			'className' => 'LdapConnectorAuthentication',
			'foreignKey' => 'auth_awareness_id'
		),
		'LdapAuthPolicies' => array(
			'className' => 'LdapConnectorAuthentication',
			'foreignKey' => 'auth_policies_id'
		)
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Ldap Connectors');
		$this->_group = parent::SECTION_GROUP_SYSTEM;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'authenticator_settings' => [
				'label' => __('LDAP Authenticator Settings')
			],
			'group_settings' => [
				'label' => __('LDAP Group Settings')
			]
		];

		$this->fieldData = array(
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('The name of the connector, for example "Corporate LDAP"')
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('Brief description for this connector')
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'options' => [$this, 'getStatuses'],
				'description' => __('If the connector is enabled is ready to be used across the system')
			],
			'host' => [
				'label' => __('LDAP Server Hostname'),
				'editable' => true,
				'description' => __('The ldap server you want to connect. If you want to use TLS then don\'t forget to include ldaps:// in front of the server name. For example ldaps://ldap.company.com. Additionally you may need to edit your ldap.conf file and include a setting for TLS_REQCERT (with value "never").')
			],
			'domain' => [
				'label' => __('Mail Domain'),
				'editable' => true,
				'description' => __('The domain used on your emails, for example mycompany.com')
			],
			'port' => [
				'label' => __('Port'),
				'editable' => true,
				'description' => __('By default we connect to the port 389. If you are using TLS on your directory you would typically use 636.'),
				'default' => 389
			],
			'ldap_bind_dn' => [
				'label' => __('LDAP Username'),
				'editable' => true,
				'description' => __('This is the username that will be used to connect to the LDAP server using the full DN. <br><br>For example: "CN=Joe Ramone,OU=People,DC=corp,DC=eramba,DC=org"')
			],
			'ldap_bind_pw' => [
				'label' => __('LDAP Password'),
				'editable' => true,
				'description' => __('This is the password for the account defined on the field above'),
				'renderHelper' => ['LdapConnectors', 'ldapPasswordField'],
			],
			'ldap_base_dn' => [
				'label' => __('LDAP Server Base DN'),
				'editable' => true,
				'description' => __('This is the base directory where queries will be executed. <br><br>If you are not sure, use the DN of the user you used to connect to AD and keep the domain part, for example  "DC=corp,DC=eramba,DC=org"')
			],
			'type' => [
				'label' => __('Type'),
				'options' => [$this, 'getTypes'],
				'editable' => true,
				'description' => __('If you are looking to authenticate users in eramba, then select "Authenticator". <br>If you have plans to use the Policy Portal or Awareness module you will need two connectors, one "Authenticator" and another "Group"'),
				'renderHelper' => ['LdapConnectors', 'typeField']
			],

			//
			// Authenticator
			'ldap_auth_filter' => [
				'label' => __('LDAP Filter: Login Account Name'),
				'editable' => true,
				'description' => __('This filter is the one eramba will use against your LDAP directory to find account names. When someone logs in eramba with the user "john.smith", this filter must be able to find that account. eramba will replace the pattern %USERNAME% with "john.smith" and run the filter.<br><br>For example, a typical filter for AD would be (&(objectcategory=user)(sAMAccountName=%USERNAME%))'),
				'default' => '(| (sn=%USERNAME%) )',
				'renderHelper' => ['LdapConnectors', 'ldapAuthFilterField'],
				'group' => 'authenticator_settings'
			],
			'ldap_auth_attribute' => [
				'label' => __('LDAP Attribute: Account Name'),
				'editable' => true,
				'description' => __('This is the LDAP attribute eramba will append to the filter defined above and is used to return the login name the filter above returns. In the example mentioned before, this attribute must return the string "john.smith"<br><br>A typical attribute in AD would be "sAMAccountName"'),
				'group' => 'authenticator_settings'
			],
			'ldap_name_attribute' => [
				'label' => __('LDAP Attribute: Full Name'),
				'editable' => true,
				'description' => __('This is the LDAP attribute eramba will append to the filter defined above and is used to return the given name of the account, following the example it should return "John Smith".<br><br>A typical attribute in AD would be "displayName"'),
				'group' => 'authenticator_settings'
			],
			'ldap_email_attribute' => [
				'label' => __('LDAP Attribute: Account Email'),
				'editable' => true,
				'description' => __('This is the LDAP attribute eramba will append to the filter defined above and is used to return the email of the account, following the example it should return "John.Smith@acme.com".<br><br>For example, a typical attribute in AD would be "mail"'),
				'group' => 'authenticator_settings'
			],
			'ldap_memberof_attribute' => [
				'label' => __('LDAP Attribute: Group Membership'),
				'editable' => true,
				'description' => __('This is the LDAP attribute eramba will append to the filter defined above and is used to return all groups in the directory where this login belongs to. <br><br>For example, a typical attribute in AD would be "memberOf"'),
				'group' => 'authenticator_settings'
			],
			//

			//
			// Group
			'ldap_grouplist_filter' => [
				'label' => __('LDAP Filter: List of Groups'),
				'editable' => true,
				'description' => __('This filter will be used to get the list of groups in your directory. <br><br>For example, a typical attribute in AD would be "(objectCategory=group)"'),
				'group' => 'group_settings'
			],
			'ldap_grouplist_name' => [
				'label' => __('LDAP Attribute: Group Name'),
				'editable' => true,
				'description' => __('This is the LDAP attribute eramba will append to the filter defined above and is used to return the name of those groups found. Important - make sure the names returned here match the same DN syntax as the filter used on the authenticator that return the group memberships for a given user account.<br><br>For example, a typical attribute in AD would be "distinguishedName" or "cn"'),
				'group' => 'group_settings'
			],
			'ldap_groupmemberlist_filter' => [
				'label' => __('LDAP Filter: Group Membership'),
				'editable' => true,
				'description' => __('This filter is used to pull the members of a group,the filter must the macro %GROUP% that indicates the group to be searched for. <br><br>For example, a typical attribute in AD would be "(&(objectCategory=user)(memberOf=CN=%GROUP%))"'),
				'group' => 'group_settings'
			],
			'ldap_group_account_attribute' => [
				'label' => __('LDAP Attribute: Group Member Account Name'),
				'editable' => true,
				'description' => __('This is the LDAP attribute eramba will append to the filter defined above and is used to return the account name for all members of a given group. Make sure the accounts returned match the syntax of the accounts used on the authenticator connector. <br><br>For example, a typical attribute in AD would be "sAMAccountName"'),
				'renderHelper' => ['LdapConnectors', 'ldapGroupAccountAttributeField'],
				'group' => 'group_settings'
			],
			'ldap_group_email_attribute' => [
				'label' => __('LDAP Attribute: Email'),
				'editable' => true,
				'description' => __('This is the LDAP attribute eramba will append to the filter defined above and is used to return the email of each account that belongs to a given group.<br><br>A typical setting for AD is "email"'),
				'group' => 'group_settings'
			],
			'ldap_group_fetch_email_type' => [
				'label' => __('Email Method'),
				'type' => 'select',
				'options' => [$this, 'getLdapConnectorEmailFetchTypes'],
				'editable' => true,
				'description' => __('By default you should be able to use the LDAP attribute defined above to obtain the email of an account, but if for some reason that is not possible then you can use the field below to define a domain name.'),
				'default' => LDAP_CONNECTOR_EMAIL_FETCH_EMAIL_ATTRIBUTE,
				'renderHelper' => ['LdapConnectors', 'ldapGroupFetchEmailTypeField'],
				'group' => 'group_settings'
			],
			'ldap_group_mail_domain' => [
				'label' => __('Mail Domain'),
				'editable' => true,
				'description' => __('Eramba will use the return value from the attribute "Account Attribute" and add this domain in order to have a complete email address.'),
				'group' => 'group_settings'
			]
			//
		);

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->textField('name', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->textField('host')
				->textField('domain')
				->textField('port')
				->textField('ldap_bind_dn')
				->textField('ldap_bind_pw')
				->textField('ldap_base_dn')
				->selectField('type', [$this, 'types'], [
					'showDefault' => true
				])
				->textField('ldap_auth_filter', [
					'label' => __('Filter to find account names')
				])
				->textField('ldap_auth_attribute', [
					'label' => __('LDAP Account Attribute')
				])
				->textField('ldap_name_attribute', [
					'label' => __('Account Full Name Attribute')
				])
				->textField('ldap_email_attribute', [
					'label' => __('Account Email Attribute')
				])
				->textField('ldap_memberof_attribute', [
					'label' => __('LDAP Memberof Attribute')
				])
				->textField('ldap_grouplist_filter', [
					'label' => __('Filter to get the list of groups')
				])
				->textField('ldap_grouplist_name', [
					'label' => __('Group Name Attribute')
				])
				->textField('ldap_groupmemberlist_filter', [
					'label' => __('Filter to get members of a group')
				])
				->textField('ldap_group_account_attribute', [
					'label' => __('Group member Account Name Attribute')
				])
				->textField('ldap_group_email_attribute', [
					'label' => __('Email Attribute')
				])
				->selectField('ldap_group_fetch_email_type', [$this, 'getLdapConnectorEmailFetchTypes'])
				->textField('ldap_group_mail_domain')
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true
				]);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getStatuses() {
		return self::statuses();
	}

	// possible statuses
	public static function statuses($value = null) {
		$options = array(
			self::STATUS_DISABLED => __('Disabled'),
			self::STATUS_ACTIVE => __('Active')
		);
		return parent::enum($value, $options);
	}
	const STATUS_DISABLED = 0;
	const STATUS_ACTIVE = 1;

	public function getTypes() {
		return self::types();
	}

	// possible types
	public static function types($value = null) {
		$options = array(
			self::TYPE_AUTHENTICATOR => __('Authenticator'),
			self::TYPE_GROUP => __('Group')
		);
		return parent::enum($value, $options);
	}
	const TYPE_AUTHENTICATOR = 'authenticator';
	const TYPE_GROUP = 'group';

	public function beforeValidate($options = array()) {
		// default validation
		$this->addListValidation('status', array_keys(self::statuses()));
		$this->addListValidation('type', array_keys(self::types()));

		$this->handleTypeValidation();

		return true;
	}

	public function getLdapConnectorEmailFetchTypes()
	{
		return getLdapConnectorEmailFetchTypes();
	}

	public function beforeSave($options = [])
	{
		$ret = true;

		if (!$this->isNewEntity() && $this->inUse($this->id) &&
			(isset($this->data['LdapConnector']['status']) && $this->data['LdapConnector']['status'] == self::STATUS_DISABLED)) {
			$ret = false;
			$this->invalidate('status', __('This connector seem to be in use and can not be disabled'));
		}

		return $ret;
	}

	/**
	 * Restrict deletion if a Connector is still in use.
	 */
	public function beforeDelete($cascade = true) {
		$ret = true;

		if ($this->inUse($this->id)) {
			$ret = false;
			$this->customDeleteMessage = __('Ldap Connector cannot be deleted because is in use.');
		}

		return $ret;
	}

	public function afterSave($created, $options = array()) {
		if (!$created) {
			Cache::clearGroup('ldap', 'ldap');
		}
	}

	private function handleTypeValidation() {
		if ($this->data['LdapConnector']['type'] == LDAP_CONNECTOR_TYPE_GROUP) {
			$this->validator()->add('ldap_grouplist_filter', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP filter is required')
			));

			$this->validator()->add('ldap_grouplist_name', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP attribute is required')
			));

			$this->validator()->add('ldap_groupmemberlist_filter', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP filter is required')
			));

			// group type validation
			$this->validator()->add('ldap_group_fetch_email_type', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('Email address configuration is required')
			));
			
			$this->addListValidation('ldap_group_fetch_email_type', array_keys(getLdapConnectorEmailFetchTypes()));

			$this->validator()->add('ldap_group_account_attribute', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP attribute is required')
			));

			if ($this->data['LdapConnector']['ldap_group_fetch_email_type'] == LDAP_CONNECTOR_EMAIL_FETCH_EMAIL_ATTRIBUTE) {
				$this->validator()->add('ldap_group_email_attribute', 'notEmpty', array(
					'rule' => 'notBlank',
					'required' => true,
					'allowEmpty' => false,
					'message' => __('This LDAP attribute is required')
				));
			}

			if ($this->data['LdapConnector']['ldap_group_fetch_email_type'] == LDAP_CONNECTOR_EMAIL_FETCH_ACCOUNT_DOMAIN) {
				$this->validator()->add('ldap_group_mail_domain', 'notEmpty', array(
					'rule' => 'notBlank',
					'required' => true,
					'allowEmpty' => false,
					'message' => __('This field is required')
				));
			}
		}
		
		elseif ($this->data['LdapConnector']['type'] == LDAP_CONNECTOR_TYPE_AUTHENTICATOR) {
			$this->validator()->add('ldap_auth_filter', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP filter is required')
			));

			$this->validator()->add('ldap_auth_attribute', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP attribute is required')
			));

			$this->validator()->add('ldap_name_attribute', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP attribute is required')
			));

			$this->validator()->add('ldap_email_attribute', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP attribute is required')
			));

			$this->validator()->add('ldap_memberof_attribute', 'notEmpty', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => __('This LDAP attribute is required')
			));
		}
	}

	public function attributesToLowercase($connector) {
		$keys = array_keys($connector);

		if (count($keys) == 1) {
			$c = $connector[$keys[0]];
		}
		else {
			$c = $connector;
		}

		$c['ldap_auth_attribute'] = $this->toLower($c['ldap_auth_attribute']);
		$c['ldap_name_attribute'] = $this->toLower($c['ldap_name_attribute']);
		$c['ldap_email_attribute'] = $this->toLower($c['ldap_email_attribute']);
		$c['ldap_memberof_attribute'] = $this->toLower($c['ldap_memberof_attribute']);
		$c['ldap_grouplist_name'] = $this->toLower($c['ldap_grouplist_name']);
		// $c['ldap_groupmemberlist_name'] = $this->toLower($c['ldap_groupmemberlist_name']);

		if (count($keys) == 1) {
			$connector[$keys[0]] = $c;
		}
		else {
			$connector = $c;
		}

		return $connector;
	}

	private function toLower($str) {
		if ($str === null) {
			return $str;
		}

		return strtolower($str);
	}

	/**
	 * Checks if an Ldap Connector is used in some part of the system.
	 */
	public function inUse($id) {
		$count = $this->AwarenessProgram->find('count', array(
			'conditions' => array(
				'AwarenessProgram.ldap_connector_id' => $id
			)
		));

		$count = $count || $this->SecurityPolicy->find('count', array(
			'conditions' => array(
				'SecurityPolicy.ldap_connector_id' => $id
			)
		));

		$count = $count || $this->LdapAuthUsers->find('count', array(
			'conditions' => array(
				'OR' => array(
					'LdapAuthUsers.auth_users_id' => $id,
					'LdapAuthUsers.auth_awareness_id' => $id,
					'LdapAuthUsers.auth_policies_id' => $id
				)
			)
		));

		//
		// Ldap Synchronization in Users section
		$LdapSynchronization = ClassRegistry::init('LdapSync.LdapSynchronization');
		$count = $count || $LdapSynchronization->find('count', [
			'conditions' => [
				'OR' => [
					'LdapSynchronization.ldap_auth_connector_id' => $id,
					'LdapSynchronization.ldap_group_connector_id' => $id
				]
			]
		]);
		//
		
		return $count;
	}

	/**
	 * Get a connector class based on array or id.
	 */
	public function getConnector($connector)
	{
		if (!is_array($connector)) {
			$data = $this->find('first', array(
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
}
