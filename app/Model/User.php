<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
App::uses('Portal', 'Model');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Security', 'Utility');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {
	public $displayField = 'full_name_with_type';

	/**
	 * Description is in the AppModel
	 */
	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'email',
		'logRecords' => true
	);

	public $name = 'User';
	public $actsAs = array(
		'Acl' => array('type' => 'both'),
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'surname', 'email', 'login', 'language', 'status', 'local_account'
			)
		),
		'CustomRoles.CustomRoles' => [
			'roles' => ['UserAccount']
		],
		'Visualisation.Visualisation',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'SystemLogs.SystemLogs',
		'ImportTool.ImportTool',
		'AdvancedFilters.AdvancedFilters'
	);

	/*public $virtualFields = array(
		'full_name' => 'CONCAT(User.name, " ", User.surname)',
	);*/

	public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'required' => true,
				'message' => 'You need to enter email in the correct format'
			),
			'notEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be left blank'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Email is already used'
			)
		),
		'login' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be left blank'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Login is already used'
			)
		),
		'name' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		'Portal' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'message' => 'At least one portal has to be assigned to the user'
			)
		),
		'Group' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'message' => 'At least one group has to be assigned to the user'
			)
		),
		// 'language' => [
		// 	'inCallableList' => [
		// 		'rule' => ['inCallableList', 'availableLangs', true],
		// 		'message' => 'This value is invalid'
		// 	]
		// ],
		'status' => [
			'inCallableList' => [
				'rule' => ['inCallableList', 'statuses', true],
				'message' => 'This value is invalid'
			]
		],
		'api_allow' => [
			'inCallableList' => [
				'rule' => ['inCallableList', 'allowApi', true],
				'message' => 'This value is invalid'
			]
		],
	);

	public $hasMany = array(
		'UserBan'
	);

	public $hasAndBelongsToMany = array(
		'Portal' => [
			'with' => 'UsersPortal',
			'className' => 'Portal',
			'joinTable' => 'users_portals',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'portal_id'
		],
		'Group' => array(
			'with' => 'UsersGroup',
			'className' => 'Group',
			'joinTable' => 'users_groups',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'group_id'
		),
		'LdapSynchronization' => [
			'joinTable' => 'users_ldap_synchronizations'
		]
	);

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_NOT_ACTIVE => __('Inactive')
        );
        return parent::enum($value, $options);
    }

    const STATUS_ACTIVE = USER_ACTIVE;
    const STATUS_NOT_ACTIVE = USER_NOTACTIVE;

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function allowApi($value = null) {
        $options = array(
            self::API_ALLOW => __('Active'),
            self::API_NOT_ALLOW => __('Inactive')
        );
        return parent::enum($value, $options);
    }

    const API_ALLOW = 1;
    const API_NOT_ALLOW = 0;

    const LOCAL_ACCOUNT = 1;
    const NOT_LOCAL_ACCOUNT = 0;

    const UNKNOWN_LOGIN = 'unknown login';

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Users');
        $this->_group = parent::SECTION_GROUP_SYSTEM;

        $this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];
		
		$this->fieldData = array(
			'UserAccount' => [
				'label' => __('User Account'),
				'editable' => false,
			],
			'full_name_with_type' => [
				'label' => __('Full Name'),
				'editable' => false,
				// 'description' => __(''),
				// 'renderHelper' => ['Users', 'nameField']
			],
			'ldap_user' => [
				'label' => __('LDAP User'),
				'type' => 'select',
				'editable' => true,
				'description' => __('System is configured to authenticate users using LDAP. You can select an LDAP user to load data and autocomplete available form fields below.'),
				'renderHelper' => ['Users', 'ldapUserField']
			],
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('First Name'),
				'renderHelper' => ['Users', 'nameField']
			],
			'surname' => [
				'label' => __('Surname'),
				'editable' => true,
				'description' => __('Surname'),
				'renderHelper' => ['Users', 'surnameField']
			],
			'email' => [
				'label' => __('Email'),
				'editable' => true,
				'description' => __('eramba sends emails for notifications and password resets.'),
				'renderHelper' => ['Users', 'emailField']
			],
			'login' => [
				'label' => __('Login Name'),
				'editable' => true,
				'description' => __('You will use this login name to get access to eramba. If you enabled LDAP authentication (System / Settings / Authentication) you need to make sure the login you enter here is the same as your LDAP login (AD login).'),
				'renderHelper' => ['Users', 'loginField']
			],
			'local_account' => [
				'label' => __('Local account'),
				'editable' => true,
				'description' => __('If you enable the checkbox this user account will have a password stored on eramba. If you uncheck this box, the password will be authenticated against LDAP, OAuth or SAML (if you enabled LDAP, OAuth or SAML on System / Setings / Authentication).'),
				'type' => 'toggle',
				'renderHelper' => ['Users', 'localAccountField']
			],
			'old_pass' => [
				'label' => __('Current password'),
				'description' => __('Enter your current password'),
				'renderHelper' => ['Users', 'oldPassField'],
				'editable' => false
			],
			'pass' => [
				'label' => __('Password'),
				'editable' => true,
				'description' => __('Set your new password.'),
				'renderHelper' => ['Users', 'passwordField']
			],
			'pass2' => [
				'label' => __('Verify your new password'),
				'editable' => false,
				'description' => __('Type your new password again.')
			],
			'Portal' => array(
				'label' => __('Portals'),
				'editable' => true,
				'options' => ['Portal', 'portals'],
				'description' => __('Select portals to which this user should has access to.'),
				'renderHelper' => ['Users', 'portalField']
			),
			'Group' => array(
				'label' => __('Groups'),
				'editable' => true,
				'description' => __('Select groups (System / Settings / Groups) for this user. Groups have access controls defined (System / Settings / Access Lists) that limit the places where a user can access on the system.'),
				'renderHelper' => ['Users', 'groupField']
			),
			'language' => [
				'label' => __('Language'),
				'editable' => false,
				'hidden' => true,
				'description' => __('Select desired language.'),
				'options' => [$this, 'getLangs']
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'options' => [$this, 'getStatuses'],
				'description' => __('Select user status. If LDAP is the authenticator accounts are managed by the remote directory, not eramba.'),
				'renderHelper' => ['Users', 'statusField']
			],
			'api_allow' => [
				'label' => __('REST APIs'),
				'editable' => true,
				'description' => __('Check to allow the use of REST APIs for this user account.'),
				'type' => 'toggle'
			],
			'system_logs' => [
				'label' => __('Audit Trails'),
				'editable' => false,
				'hidden' => true
			],
			'LdapSynchronization' => [
				'label' => __('LDAP Synchronization'),
				'editable' => false,
				'hidden' => true
			],
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Users'),
			'pdf_file_name' => __('users'),
			'csv_file_name' => __('users'),
			'use_new_filter' => true
		);

		parent::__construct($id, $table, $ds);

		$this->virtualFields['full_name'] = 'CONCAT('.$this->alias.'.name, " ", '.$this->alias.'.surname)';
		$this->virtualFields['full_name_with_type'] = "CONCAT(`{$this->alias}`.`name`, ' ', `{$this->alias}`.`surname`, ' ', '(" . __('User') . ")')";
		$this->order = [$this->escapeField('name') . ' ASC', $this->escapeField('surname') . ' ASC'];
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->textField('login', [
					'label' => __('Login'),
					'showDefault' => true
				])
				->nonFilterableField('system_logs')
				->textField('name', [
					'showDefault' => true
				])
				->textField('surname', [
					'showDefault' => true
				])
				->textField('email', [
					'showDefault' => true
				])
				->selectField('local_account', [$this, 'getStatusFilterOption'], [
					'showDefault' => true
				])
				->multipleSelectField('Portal', [ClassRegistry::init('Portal'), 'portals'], [
					'showDefault' => true
				])
				->multipleSelectField('Group', [ClassRegistry::init('Group'), 'getList'], [
					'showDefault' => true
				])
				// ->multipleSelectField('language', [$this, 'getLangs'], [
				// 	'showDefault' => true
				// ])
				->selectField('status', [$this, 'getStatuses'], [
					'showDefault' => true
				])
			->group('LdapSynchronization', [
				'name' => __('Ldap Synchronization')
			])
				->multipleSelectField('LdapSynchronization', [ClassRegistry::init('LdapSync.LdapSynchronization'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('LdapSynchronization-ldap_auth_connector_id', [ClassRegistry::init('LdapSync.LdapSynchronization'), 'getLdapAuthConnectors'])
				->multipleSelectField('LdapSynchronization-ldap_group_connector_id', [ClassRegistry::init('LdapSync.LdapSynchronization'), 'getLdapGroupConnectors'])
				->textField('LdapSynchronization-ldap_group', [
					'label' => __('LDAP Group')
				])
				->selectField('LdapSynchronization-status', [ClassRegistry::init('LdapSync.LdapSynchronization'), 'getStatuses'], [
					'label' => __('LDAP Synchronization Status')
				]);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getImportToolConfig()
	{
		return [
			'User.name' => [
				'name' => __('Name'),
				'headerTooltip' => __('This field is mandatory')
			],
			'User.surname' => [
				'name' => __('Surname'),
				'headerTooltip' => __('This field is optional, you can leave it blank if you want to')
			],
			'User.email' => [
				'name' => __('Email'),
				'headerTooltip' => __('This field is mandatory')
			],
			'User.login' => [
				'name' => __('Login Name'),
				'headerTooltip' => __('This field is mandatory')
			],
			'User.pass' => [
				'name' => __('Password'),
				'headerTooltip' => __('This field is mandatory only if local_account is set to "1". Passwords must be between 8 and 30 characters long and only alphanumeric characters with at least one number and optionally, include one of the following: "!@#$%^&()][".')
			],
			'User.local_account' => [
				'name' => __('Local Account'),
				'headerTooltip' => __(
					'This field is mandatory. If you set this to "1" user account will have a password stored on eramba, if you set this to "0" the password will be authenticated against LDAP, OAuth or SAML.'
				)
			],
			'User.Portal' => [
				'name' => __('Portal'),
				'model' => 'Portal',
				'headerTooltip' => __(
					'This field is mandatory, accepts multiple portal IDs separated by "|", select from following IDs: %s',
					ImportToolModule::formatList(
						Portal::portals()
					)
				)
			],
			'User.Group' => [
				'name' => __('Group'),
				'model' => 'Group',
				'headerTooltip' => __('This field is mandatory, accepts multiple portal names separated by "|". You need to enter the name of a Group, you can find them at System / Settings / Groups'),
				'objectAutoFind' => true
			],
			// 'User.language' => [
			// 	'name' => __('Language'),
			// 	'headerTooltip' => __(
			// 		'This field is mandatory, set one of the following values: %s',
			// 		ImportToolModule::formatList(availableLangs(), false)
			// 	)
			// ],
			'User.status' => [
				'name' => __('Status'),
				'headerTooltip' => __(
					'This field is mandatory, set one of the following values: %s',
					ImportToolModule::formatList(self::statuses(), false)
				)
			],
			'User.api_allow' => [
				'name' => __('Allow REST APIs'),
				'headerTooltip' => __(
					'This field is mandatory, set one of the following values: %s',
					ImportToolModule::formatList(self::allowApi(), false)
				)
			],
		];
	}

	public function getNotificationSystemConfig()
	{
		return [
			'macros' => true,
			'notifications' => [
				'new_user' => [
					'type' => NOTIFICATION_TYPE_WARNING,
					'className' => '.NewUser',
					'label' => __('User Created')
				],
				'password_change' => [
					'type' => NOTIFICATION_TYPE_WARNING,
					'className' => '.PasswordChange',
					'label' => __('Password Changed')
				]
			]
		];
	}

	const SYSTEM_LOG_LOGIN_SUCCESS = 1;
	const SYSTEM_LOG_LOGIN_FAIL = 2;
	const SYSTEM_LOG_LOGIN_BRUTE_FORCE_BLOCK = 3;
	const SYSTEM_LOG_PASSWORD_CHANGE = 4;

	public function getSystemLogsConfig() {
		return [
			'subModel' => [
				'class' => 'Portal',
			],
			'logs' => [
				self::SYSTEM_LOG_LOGIN_SUCCESS => [
					'action' => self::SYSTEM_LOG_LOGIN_SUCCESS,
					'label' => __('Success Login'),
					'message' => __('Success login as "%s".')
				],
				self::SYSTEM_LOG_LOGIN_FAIL => [
					'action' => self::SYSTEM_LOG_LOGIN_FAIL,
					'label' => __('Failed login'),
					'message' => __('Failed login as "%s".')
				],
				self::SYSTEM_LOG_LOGIN_BRUTE_FORCE_BLOCK => [
					'action' => self::SYSTEM_LOG_LOGIN_BRUTE_FORCE_BLOCK,
					'label' => __('Brute Force Bocked Login'),
					'message' => __('Blocked login as "%s".')
				],
				self::SYSTEM_LOG_PASSWORD_CHANGE => [
					'action' => self::SYSTEM_LOG_PASSWORD_CHANGE,
					'label' => __('Password Change'),
					'message' => __('User "%s" has changed password.')
				],
			],
		];
	}

	public function getPortals() {
		return Portal::portals();
	} 

	/**
	 * Alphanumeric validation that can optionally include following characters: !@#$%^&()][
	 */
	public function alphaNumericCustomized($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];
        
        $validate = preg_match('/[A-Za-z]/', $value);
        $validate &= preg_match('/[0-9]/', $value);
        $validate &= preg_match('|^[0-9a-zA-Z!@#\$%\^&\(\)\]\[]*$|', $value);

        return $validate;
    }

    /**
     * Check if entered old password is equal to password of logged user
     * @param  array   $check  Field name => entered value
     * @param  integer $userId Current logged in user ID (use only when currentUser() is not available - e.g. when multiple session is presented from multiple portals)
     * @return boolean
     */
    public function oldPassCheck($check, $userId = null)
	{
		$value = array_values($check);
        $value = $value[0];

        $user = $this->find('first', [
        	'conditions' => [
        		'User.id' => !empty($userId) ? $userId : $this->currentUser('id')
        	]
        ]);
        $currentPass = $user['User']['password'];
		if ($value != '' && $currentPass == Security::hash($value, 'blowfish', $currentPass)) {
			return true;
		} else {
			return false;
		}
	}

	public function beforeDelete($cascade = true) {
		$ret = true;

		$ret &= $this->_replaceUserFields();

		return $ret;
	}

	protected function _replaceUserFields()
	{
		$UserFieldsUser = ClassRegistry::init('UserFields.UserFieldsUser');
		$UserFieldsGroup = ClassRegistry::init('UserFields.UserFieldsGroup');

		$replaceList = $UserFieldsUser->find('all', [
			'conditions' => [
				'UserFieldsUser.user_id' => $this->id,
				'UserFieldsUser.model !=' => [
					'VisualisationShareUser',
					'VisualisationShareGroup',
					'VisualisationShare'
				]
			],
			'fields' => [
				'model',
				'foreign_key',
				'field'
			],
			'recursive' => -1
		]);

		$ret = true;
		foreach ($replaceList as $item) {
			$allUsers = $UserFieldsUser->find('list', [
				'conditions' => [
					'UserFieldsUser.model' => $item['UserFieldsUser']['model'],
					'UserFieldsUser.foreign_key' => $item['UserFieldsUser']['foreign_key'],
					'UserFieldsUser.field' => $item['UserFieldsUser']['field']
				],
				'fields' => [
					'user_id', 'user_id'
				],
				'recursive' => -1
			]);

			$allGroups = $UserFieldsGroup->find('list', [
				'conditions' => [
					'UserFieldsGroup.model' => $item['UserFieldsUser']['model'],
					'UserFieldsGroup.foreign_key' => $item['UserFieldsUser']['foreign_key'],
					'UserFieldsGroup.field' => $item['UserFieldsUser']['field'] . 'Group'
				],
				'fields' => [
					'group_id', 'group_id'
				],
				'recursive' => -1
			]);

			unset($allUsers[$this->id]);
			$allUsers[] = ADMIN_ID;
			$allUsers = array_unique($allUsers);

			$userFieldData = [];
			foreach ($allUsers as $user) {
				$userFieldData[] = 'User-' . $user;
			}

			foreach ($allGroups as $group) {
				$userFieldData[] = 'Group-' . $group;
			}

			$saveData = [
                $item['UserFieldsUser']['model'] => [
                    'id' => $item['UserFieldsUser']['foreign_key'],
                    $item['UserFieldsUser']['field'] => $userFieldData
                ]
            ];

            $M = ClassRegistry::init($item['UserFieldsUser']['model']);
            if ($M->Behaviors->loaded('Utils.SoftDelete')) {
            	$M->Behaviors->disable('Utils.SoftDelete');
            }

            $ret &= $M->saveAssociated($saveData, [
                'validate' => 'first',
                'atomic' => true,
                'deep' => true,
                'fieldList' => [
                	$item['UserFieldsUser']['field']
                ]
            ]);

            if ($M->Behaviors->loaded('Utils.SoftDelete')) {
            	$M->Behaviors->enable('Utils.SoftDelete');
            }
		}

		// reviews special case
		$ret &= (bool) ClassRegistry::init('Review')->updateAll(array(
			'Review.user_id' => ADMIN_ID
		), array(
			'Review.user_id' => $this->id
		));

		// issues special case
		$ret &= (bool) ClassRegistry::init('Issue')->updateAll(array(
			'Issue.user_id' => ADMIN_ID
		), array(
			'Issue.user_id' => $this->id
		));

		return $ret;
	}

	/**
	 * we check local account value before validation.
	 */
	public function beforeValidate($options = array())
	{
		if (isset($this->data['User']['id']) && $this->data['User']['id'] == ADMIN_ID) {
			$this->validator()->remove('Portal');
		}

		if (!isset($this->data['User']['id']) && !empty($this->id)) {
			$this->data['User']['local_account'] = $this->field('local_account');
		}

		// Handle empty status field
		if (!isset($this->data['User']['status'])) {
			$this->data['User']['status'] = USER_ACTIVE;
		}

		//
		// If LDAP, OAuth or SAML authentication is enabled for users, disable password validation
		$auth = ClassRegistry::init('LdapConnectorAuthentication');
		$count = $auth->find('count', array(
			'conditions' => array(
				'OR' => array(
					'LdapConnectorAuthentication.auth_users' => '1',
					'LdapConnectorAuthentication.oauth_google' => '1',
					'LdapConnectorAuthentication.auth_saml' => '1'
				)
			),
			'recursive' => -1
		));
		//
		
		//
		// Add password validation
		if (isset($options['import']) && $options['import'] == true) {
			$this->validator()->remove('old_pass');
			$this->validator()->remove('pass');
		}

		if (!$count) { // Ldap is off
			if (!isset($this->data['User']['local_account']) || !empty($this->data['User']['local_account'])) { // Local account is not set at all or if it is set, it is not empty
				if ($this->isNewEntity()) { // It is add action (user ID is not set or empty)
					// User needs to fill password field
					$this->addPasswordRequiredValidationRule();
					$this->addPasswordValidationRule(isset($options['import']) && $options['import'] == true ? false : true);
				}
			}
		}

		if (!$this->isNewEntity()) {
			if (!empty($this->data['User']['old_pass'])) {
				$this->addOldPassValidationRule(isset($options['user_id']) ? $options['user_id'] : null);
			}

			if (!empty($this->data['User']['pass'])) {
				$this->addPasswordValidationRule();
			}
		}
		//
		
		if (isset($this->data['User']['Group'])) {
			$this->invalidateRelatedNotExist('Group', 'Group', $this->data['User']['Group']);
		}

		if (isset($this->data['User']['Portal'])) {
			$this->invalidateRelatedNotExist('Portal', 'Portal', $this->data['User']['Portal']);
		}

		if (isset($this->data['User']['local_account'])
			&& is_string($this->data['User']['local_account'])
			&& $this->data['User']['local_account'] !== ((string) self::LOCAL_ACCOUNT)
			&& $this->data['User']['local_account'] != ((string) self::NOT_LOCAL_ACCOUNT)
		) {
			$this->invalidate('local_account', __('Not allowed value.'));
		}
	}

	public function beforeSave($options = array())
	{
		// Hash user password
		$this->hashPassword();

		//
		// Force new local user to change his password after first login
		if ((!isset($options['autoSetDefaultPasswordState']) ||
			$options['autoSetDefaultPasswordState'] == true) &&
			isset($this->data['User'])) {
			if (!empty($this->data['User']['id'])) {
				$loggedUser = CakeSession::read('Auth.User');
				if ($this->data['User']['id'] != $loggedUser['id']) {
					$oldUserData = $this->find('first', [
						'fields' => [
							'id', 'local_account'
						],
						'conditions' => [
							'User.id' => $this->data['User']['id'],
							'User.default_password' => 0
						]
					]);

					if (!empty($oldUserData) &&
						(($oldUserData['User']['local_account'] == 0 && $this->data['User']['local_account'] == 1) ||
						 ($oldUserData['User']['local_account'] == 1 && !empty($this->data['User']['password'])))) {
						$this->data['User']['default_password'] = 1;
					}
				}
			} else if (isset($this->data['User']['local_account']) &&
					$this->data['User']['local_account'] == 1) {
				$this->data['User']['default_password'] = 1;
			}
		}
		//
		
    	return true;
	}

	protected function hashPassword()
	{
		if (!empty($this->data['User']['pass'])) {
			$this->data['User']['password'] = Security::hash($this->data['User']['pass']);

			//
			// Remove redundant fields
			unset($this->data['User']['old_pass']);
			unset($this->data['User']['pass']);
			unset($this->data['User']['pass2']);
			//
		}
	}

	/**
	 * After save callback.
	 */
	public function afterSave($created, $options = array()) {
		$ret = true;

		if ($created) {
			// for a new user we also create a custom role related row that will act as ACL node.
			$CustomRolesUser = ClassRegistry::init('CustomRoles.CustomRolesUser');
			$ret &= $CustomRolesUser->syncSingleObject($this->id);

			$VisualisationShareUser = ClassRegistry::init('Visualisation.VisualisationShareUser');
			$ret &= $VisualisationShareUser->share($this->id, [$this->alias, $this->id], false);
		}

		Cache::clearGroup('Visualisation', 'visualisation');

		// log password change action
		if (!empty($this->data['User']['password'])) {
			$user = $this->find('first', [
				'conditions' => [
					'User.id' => $this->id
				],
				'fields' => ['User.id', 'User.login'],
				'recursive' => -1,
				'contain' => []
			]);

			if (!empty($user)) {
				$this->createSystemLog(self::SYSTEM_LOG_PASSWORD_CHANGE)
					->result($user['User']['login'])
					->message([$user['User']['login']])
					->log();
			}
		}

		return $ret;
	}

	public function getLangs() {
		return availableLangs();
	}

	public function getStatuses() {
		return array(
			USER_ACTIVE => __('Active'),
			USER_NOTACTIVE => __('Inactive')
		);
	}


	public function getGroups() {
		$data = $this->Group->find('list', array(
			'order' => array('Group.name' => 'ASC'),
			'fields' => array('Group.id', 'Group.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function addOldPassValidationRule($userId = null)
	{
		$this->validator()->add('old_pass', array(
			'old_pass_check' => [
				'rule' => [
					'oldPassCheck', $userId
				],
				'message' => 'Old password is wrong.'
			]
		));
	}

	public function addPasswordValidationRule($compare = true)
	{
		$rule = [
			'between' => [
				'rule' => ['between', 8, 30],
				'message' => 'Passwords must be between 8 and 30 characters long.'
			],
			'alphaNumericCustomized' => [
				'rule' => 'alphaNumericCustomized',
				'message' => 'Password must be only alphanumeric characters with at least one number and optionally, include one of the following: "!@#$%^&()]["'
			]
		];

		if ($compare) {
			$rule['compare'] = [
				'rule' => ['comparePassword', 'pass2'],
				'message' => 'Password and verify password must be same.'
			];
		}
		$this->validator()->add('pass', $rule);
	}

	public function addOldPassRequiredValidationRule()
	{
		$this->validator()->add('old_pass', 'required', [
			'rule' => 'notBlank',
			'required' => true,
			'message' => __('You have to enter your current password.')
		]);
	}

	public function addPasswordRequiredValidationRule()
	{
		$this->validator()->add('pass', 'required', [
			'rule' => 'notBlank',
			'required' => true,
			'message' => __('You have to enter some password.')
		]);
	}

	/**
	 * Change all owner fields that belongs to the current user $this->id, to Admin.
	 *
	 * @deprecated in favour of User::_replaceUserFields() method
	 */
	private function makeItemOwnersAdmin() {
		$ownersData = array(
			'Risk' => array('user_id', 'guardian_id'),
			'ThirdPartyRisk' => array('user_id', 'guardian_id'),
			'BusinessContinuity' => array('user_id', 'guardian_id'),
			'SecurityIncident' => array('user_id'),
			'SecurityService' => 'user_id',
			'SecurityServiceAudit' => 'user_id',
			'SecurityServiceAuditImprovement' => array('user_id'),
			'SecurityServiceMaintenance' => 'user_id',
			'Goal' => 'owner_id',
			'GoalAudit' => 'user_id',
			'GoalAuditImprovement' => array('user_id'),
			'Asset' => 'asset_owner_id',
			'BusinessContinuityPlan' => array('owner_id', 'launch_responsible_id', 'sponsor_id'),
			'BusinessContinuityPlanAudit' => 'user_id',
			'BusinessContinuityPlanAuditImprovement' => array('user_id'),
			'BusinessContinuityTask' => array('awareness_role'),
			'Project' => 'user_id',
			'ProjectAchievement' => 'user_id',
			'Review' => 'user_id',
			'Issue' => 'user_id',
			'Attachment' => 'user_id',
			'ComplianceAudit' => 'auditor_id',
			'NotificationSystemItemFeedback' => 'user_id',
			'RiskExceptions' => array('author_id'),
			'Scope' => array('ciso_role_id', 'ciso_deputy_id', 'board_representative_id', 'board_representative_deputy_id'),
			'SecurityPolicy' => array('author_id'),
			'SystemRecord' => array('user_id'),
			'ComplianceAuditAuditeeFeedback' => array('user_id'),
			'AdvancedFilterUserSetting' => array('user_id')
			
		);
		$ret = true;

		foreach ($ownersData as $model => $field) {
			$ret = $this->makeFieldAdmin($model, $field);
		}

		return $ret;
	}

	/**
	 * @deprecated
	 */
	public function makeFieldAdmin($model, $field) {
		$ret = true;
		if (is_array($field)) {
			foreach ($field as $f) {
				$ret &= $this->makeFieldAdmin($model, $f);
			}

			return $ret;
		}

		$tmpClass = ClassRegistry::init($model);
		if (!$tmpClass->schema($field)) {
			return true;
		} 

		return $tmpClass->updateAll(array(
			$model . '.' . $field => ADMIN_ID
		), array(
			$model . '.' . $field => $this->id
		));
	}

	public function comparePassword($pass1 = null, $pass2 = null) {

		foreach ($pass1 as $key => $value) {
			if ($value != $this->data[$this->name][$pass2]) {
				return false;
			}
			else continue;
		}
		return true;
	}

	/**
	 * Checks if a user is associated in a restricted table relation.
	 * @deprecated
	 */
	public function hasRestrictAssoc($userId) {
		$securityIncident = ClassRegistry::init('SecurityIncident');
		$count = $securityIncident->find('count', array(
			'conditions' => array(
				'user_id' => $userId
			),
			'recursive' => -1
		));
		if ($count) {
			return true;
		}

		$controlAudit = ClassRegistry::init('SecurityServiceAudit');
		$count = $controlAudit->find('count', array(
			'conditions' => array(
				'user_id' => $userId
			),
			'recursive' => -1
		));
		if ($count) {
			return true;
		}

		$tpRisk = ClassRegistry::init('ThirdPartyRisk');
		$count = $tpRisk->find('count', array(
			'conditions' => array(
				'user_id' => $userId
			),
			'recursive' => -1
		));
		if ($count) {
			return true;
		}

		$businessContinuity = ClassRegistry::init('BusinessContinuity');
		$count = $businessContinuity->find('count', array(
			'conditions' => array(
				'user_id' => $userId
			),
			'recursive' => -1
		));
		if ($count) {
			return true;
		}

		$goalAudit = ClassRegistry::init('GoalAudit');
		$count = $goalAudit->find('count', array(
			'conditions' => array(
				'user_id' => $userId
			),
			'recursive' => -1
		));
		if ($count) {
			return true;
		}

		$risk = ClassRegistry::init('Risk');
		$count = $risk->find('count', array(
			'conditions' => array(
				'user_id' => $userId
			),
			'recursive' => -1
		));
		if ($count) {
			return true;
		}

		return false;
	}

	public function saveBlockedField($id, $blocked) {
		$this->id = $id;

		// $this->pushStatusRecords();
		$ret = $this->saveField('blocked', $blocked, array('validate' => false, 'callbacks' => 'before'));
		// $this->holdStatusRecords();

		return $ret;
	}

	public function parentNode($type) {
		if ($type == 'Aro') {
			if (!$this->id && empty($this->data)) {
				return null;
			}

			$groups = array();
			if (isset($this->data['Group']['Group']) && is_array($this->data['Group']['Group'])) {
				$groups = $this->data['Group']['Group'];
			}
			else {
				$groups = $this->find('first', array(
					'conditions' => array(
						$this->alias . '.id' => $this->id
					),
					'fields' => [
						$this->alias . '.id'
					],
					'contain' => array(
						'Group' => array(
							'fields' => array(
								'Group.id'
							)
						)
					)
				));

				$groups = Hash::extract($groups, 'Group.{n}.id');
			}
			if (empty($groups)) {
				return null;
			}
			else {
				$groups = array_values($groups);
				return array('Group' => array('id' => $groups[0]));
			}
		}

		if ($type == 'Aco') {
			return parent::parentNode($type);
		}
	}

	public function checkBlockedStatus($id) {
		$this->bindModel( array(
			'hasMany' => array(
				'UserBan'
			)
		) );
		$this->Behaviors->attach('Containable');

		$fromTime = CakeTime::format( 'Y-m-d H:i:s', CakeTime::fromString( '-' . BRUTEFORCE_SECONDS_AGO . ' seconds' ) );
		$user = $this->find( 'first', array(
			'fields' => array('id', 'blocked'),
			'conditions' => array(
				'User.id' => $id
			),
			'contain' => array(
				'UserBan' => array(
					'conditions' => array(
						'UserBan.until >' => CakeTime::format( 'Y-m-d H:i:s', CakeTime::fromString( 'now' ) )
					)
				)
			),
		) );

		if ( empty( $user ) ) {
			return true;
		}

		if (empty($user['UserBan']) && $user['User']['blocked']) {
			return (boolean) $this->saveBlockedField($user['User']['id'], '0');
		}

		return true;
	}

	public function checkAllBlockedStatuses() {
		$data = $this->find('list', array(
			'fields' => array('id')
		));

		$ret = true;
		foreach ($data as $id) {
			$ret &= $this->checkBlockedStatus($id);
		}

		return $ret;
	}

	/**
	 * Unblock user that has a brute force block.
	 */
	public function unblockBan($userId) {
		$ret = true;

		$ret &= $this->UserBan->deleteAll(array(
			'UserBan.user_id' => $userId,
			'UserBan.until >' => '"' . CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now')) . '"'
		));

		$ret &= $this->saveBlockedField($userId, '0');

		return $ret;
	}

	/**
	 * Returns array of user emails as a list.
	 */
	public function getEmails($ids = []) {
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		return $this->find('list', [
			'conditions' => [
				$this->alias . '.id' => $ids
			],
			'fields' => [
				$this->alias . '.' . $this->primaryKey,
				$this->alias . '.email'
			],
			'recursive' => -1
		]);
	}

	public function getUserByLogin($login)
	{
		return  $this->find('first', [
			'conditions' => [
				'User.login' => trim($login)
			],
			'recursive' => -1
		]);
	}

	public function getIdByLogin($login)
	{
		$user = $this->find('first', [
			'conditions' => [
				'User.login' => trim($login)
			],
			'fields' => [
				'User.id'
			],
			'recursive' => -1
		]);

		return (!empty($user)) ? $user['User']['id'] : null;
	}
}
