<?php
App::uses('LdapSyncAppModel', 'LdapSync.Model');
App::uses('LdapConnector', 'Model');
App::uses('Portal', 'Model');
App::uses('Hash', 'Utility');

class LdapSynchronization extends LdapSyncAppModel
{
	public $displayField = 'name';

	/**
	 * Save id of the last deleted item (e.g. for afterDelete callback)
	 * @var int|null
	 */
	private $deletedEntityId = null;

	/**
	 * Save name of the last deleted item (e.g. for afterDelete callback)
	 * @var string|null
	 */
	private $deletedEntityName = null;

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description', 'ldap_group'
			)
		),
		'AuditLog.Auditable',
		'SystemLogs.SystemLogs',
		'ModuleDispatcher' => [
			'behaviors' => [
				'NotificationSystem.NotificationSystem'
			]
		]
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'ldap_group_connector_id' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'You have to choose Ldap Group Connector'
			]
		],
		'ldap_auth_connector_id' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'You have to choose Ldap Authenticator Connector'
			]
		],
		'ldap_group' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'You have to choose one group from available list of LDAP groups'
			]
		],
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
		'no_user_action' => [
			'inCallableList' => [
				'rule' => ['inCallableList', 'noUserActionOptions', true],
				'message' => 'This value is invalid'
			]
		]
	);

	public $belongsTo = [
		'LdapGroupConnector' => [
			'className' => 'LdapConnector',
			'conditions' => [
				'LdapGroupConnector.type' => 'group'
			]
		],
		'LdapAuthConnector' => [
			'className' => 'LdapConnector',
			'conditions' => [
				'LdapAuthConnector.type' => 'authenticator'
			]
		]
	];

	public $hasAndBelongsToMany = array(
		'Group' => [
			'joinTable' => 'ldap_synchronizations_groups'
		],
		'Portal' => [
			'joinTable' => 'ldap_synchronizations_portals'
		],
		'User' => [
			'joinTable' => 'users_ldap_synchronizations'
		]
	);

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

    const NO_USER_ACTION_DISABLE = 1;
    const NO_USER_ACTION_DELETE = 2;
    const NO_USER_ACTION_IGNORE = 3;

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('LDAP Synchronizations');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'user_defaults' => [
				'label' => __('User Defaults')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'description' => __('Name this Sync process'),
				'editable' => true,
				'renderHelper' => ['LdapSync.LdapSynchronization', 'nameField']
			],
			'description' => [
				'label' => __('Description'),
				'description' => __('OPTIONAL: Provide a description for this sync process'),
				'editable' => true
			],
			'ldap_group_connector_id' => [
				'label' => __('LDAP Group Connector'),
				'description' => __('Choose an LDAP Group Connector that is enabled. LDAP Connectors are defined at System / Settings / LDAP Connectors'),
				'editable' => true,
				'empty' => __('Select an LDAP Group Connector'),
				'options' => [$this, 'getLdapGroupConnectors'],
				'renderHelper' => ['LdapSync.LdapSynchronization', 'ldapConnectorIdField']
			],
			'ldap_auth_connector_id' => [
				'label' => __('LDAP Auth Connector'),
				'description' => __('Choose an LDAP Authenticator Connector that is enabled. LDAP Connectors are defined at System / Settings / LDAP Connectors'),
				'editable' => true,
				'empty' => __('Select and LDAP Authenticator connector'),
				'options' => [$this, 'getLdapAuthConnectors']
			],
			'ldap_group' => [
				'label' => __('Ldap Group'),
				'description' => __('Select the group you want to sync. If your group is not listed you might need to adjust your LDAP Connector settings'),
				'editable' => true,
				'type' => 'select',
				'empty' => __('Select Ldap Group')
			],
			'Group' => [
				'label' => __('Groups'),
				'description' => __('Select one or more groups that the sync process will assign to users created on eramba'),
				'editable' => true,
				'options' => [$this, 'getGroups'],
				'group' => 'user_defaults'
			],
			'Portal' => [
				'label' => __('Portals'),
				'description' => __('Select one or more portals that the sync process will assign to users created on eramba'),
				'editable' => true,
				'options' => [$this, 'getPortals'],
				'group' => 'user_defaults'
			],
			'no_user_action' => [
				'label' => __('Non-existent user action'),
				'editable' => true,
				'options' => [$this, 'noUserActionOptions'],
				'description' => __('Select the action to take for users found on eramba which are not found on the selected group<br>Disable: will disable (not delete) the account in eramba<br>Delete: will permanently delete the account in eramba'),
				'default' => self::NO_USER_ACTION_IGNORE
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'options' => [$this, 'getStatuses'],
				'description' => __('Select Active if you want this sycn process to take effect as soon as its saved')
			],
			'system_logs' => [
				'label' => __('Audit Trails'),
				'editable' => false,
				'hidden' => true
			]
		];

		parent::__construct($id, $table, $ds);
	}

	const SYSTEM_LOG_LDAP_SYNC_STARTED = 1;
	const SYSTEM_LOG_LDAP_SYNC_FINISHED = 2;
	const SYSTEM_LOG_LDAP_SYNC_FAIL_GROUP_NOT_FOUND = 3;
	const SYSTEM_LOG_LDAP_ACCOUNT_FOUND = 4;
	const SYSTEM_LOG_LDAP_ACCOUNT_NOT_FOUND = 5;
	const SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_SUCCESS = 6;
	const SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_FAIL = 7;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_EXISTS = 8;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_SUCCESS = 9;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_FAIL = 10;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_SUCCESS = 11;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_FAIL = 12;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_DISABLED = 13;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_SUCCESS = 14;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_FAIL = 15;
	const SYSTEM_LOG_LDAP_SYNC_CREATED = 16;
	const SYSTEM_LOG_LDAP_SYNC_DELETED = 17;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED = 18;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_SUCCESS = 19;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_FAIL = 20;
	const SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED_LOCAL = 21;

	public function getSystemLogsConfig() {
		return [
			'logs' => [
				self::SYSTEM_LOG_LDAP_SYNC_STARTED => [
					'action' => self::SYSTEM_LOG_LDAP_SYNC_STARTED,
					'label' => __('Sync process started'),
					'message' => __('Sync process "%s" started.')
				],
				self::SYSTEM_LOG_LDAP_SYNC_FINISHED => [
					'action' => self::SYSTEM_LOG_LDAP_SYNC_FINISHED,
					'label' => __('Sync process finished'),
					'message' => __('Sync process "%s" finished.')
				],
				self::SYSTEM_LOG_LDAP_SYNC_FAIL_GROUP_NOT_FOUND => [
					'action' => self::SYSTEM_LOG_LDAP_SYNC_FAIL_GROUP_NOT_FOUND,
					'label' => __('Sync process failed'),
					'message' => __('Sync process "%s" failed because group "%s" no longer exists on LDAP server.')
				],
				self::SYSTEM_LOG_LDAP_ACCOUNT_FOUND => [
					'action' => self::SYSTEM_LOG_LDAP_ACCOUNT_FOUND,
					'label' => __('LDAP Account Found'),
					'message' => __('Sync process "%s": User account "%s" found on LDAP group "%s".')
				],
				self::SYSTEM_LOG_LDAP_ACCOUNT_NOT_FOUND => [
					'action' => self::SYSTEM_LOG_LDAP_ACCOUNT_NOT_FOUND,
					'label' => __('LDAP Account Not Found'),
					'message' => __('Sync process "%s": User account "%s" was not found on LDAP group "%s" - we are %s the account.')
				],
				self::SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_SUCCESS => [
					'action' => self::SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_SUCCESS,
					'label' => __('LDAP Account Ready'),
					'message' => __('Sync process "%s": User account "%s" does not exist in eramba - mandatory fields looks ok, ready to create.')
				],
				self::SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_FAIL => [
					'action' => self::SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_FAIL,
					'label' => __('LDAP Account Not Ready'),
					'message' => __('Sync process "%s": User account "%s" does not exist in eramba - mandatory fields missing: %s, we are not creating the account.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_EXISTS => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_EXISTS,
					'label' => __('LDAP Account Already Exists'),
					'message' => __('Sync process "%s": User account "%s" exists in eramba we are not creating it again.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_SUCCESS => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_SUCCESS,
					'label' => __('Account Created Successfully'),
					'message' => __('Sync process "%s": User account "%s" has been created successfully.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_FAIL => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_FAIL,
					'label' => __('Account Creating Failed'),
					'message' => __('Sync process "%s": User account "%s" has not been created.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_SUCCESS => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_SUCCESS,
					'label' => __('Account Disabled Successfully'),
					'message' => __('Sync process "%s": User account: "%s" found in eramba and not found on group "%s" - account has been disabled')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_FAIL => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_FAIL,
					'label' => __('Account Disabling Failed'),
					'message' => __('Sync process "%s": An error occured - account "%s" has not been disabled.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_DISABLED => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_DISABLED,
					'label' => __('Account is already disabled'),
					'message' => __('Sync process "%s": User account "%s" is already disabled - no change required.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_SUCCESS => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_SUCCESS,
					'label' => __('Account Deleted Successfully'),
					'message' => __('Sync process "%s": User account: "%s" found in eramba and not found on group "%s" - account has been deleted')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_FAIL => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_FAIL,
					'label' => __('Account Deleting Failed'),
					'message' => __('Sync process "%s": An error occured - account "%s" has not been deleted.')
				],
				self::SYSTEM_LOG_LDAP_SYNC_CREATED => [
					'action' => self::SYSTEM_LOG_LDAP_SYNC_CREATED,
					'label' => __('LDAP Sync Process Created'),
					'message' => __('An LDAP Sync process has been created by the name of "%s", the first sync will take place on the next hourly cron run or whenever the administrator forces the sync manually.')
				],
				self::SYSTEM_LOG_LDAP_SYNC_DELETED => [
					'action' => self::SYSTEM_LOG_LDAP_SYNC_DELETED,
					'label' => __('LDAP Sync Process Deleted'),
					'message' => __('An LDAP Sync process by the name of "%s" has been deleted from the system.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED,
					'label' => __('Account ignored'),
					'message' => __('Sync process "%s": User account "%s" found in eramba and not found on group "%s" - account has been ignored.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED_LOCAL => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED_LOCAL,
					'label' => __('Account ignored local'),
					'message' => __('Sync process "%s": User account "%s" found in eramba as local account and also found on group "%s" - account has been ignored.')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_SUCCESS => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_SUCCESS,
					'label' => __('Account updated successfully'),
					'message' => __('Sync process "%s": User account: "%s" has been updated successfully')
				],
				self::SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_FAIL => [
					'action' => self::SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_FAIL,
					'label' => __('Account updating failed'),
					'message' => __('Sync process "%s": An error occured - account "%s" has not been updated.')
				]
			],
		];
	}

	public function getNotificationSystemConfig()
	{
		return [
			'macros' => false,
			'notifications' => [
				'ldap_sync_failed' => [
					'type' => NOTIFICATION_TYPE_WARNING,
					'className' => 'LdapSync.LdapSyncFailed',
					'label' => __('LDAP Sync Failed')
				]
			]
		];
	}

	const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;

	public static function getStatuses($value = null)
	{
        $options = array(
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_NOT_ACTIVE => __('Inactive')
        );
        return parent::enum($value, $options);
    }

	public function getLdapGroupConnectors()
	{
		$ldapConnectors = $this->LdapGroupConnector->find('list', [
			'conditions' => [
				'LdapGroupConnector.type' => 'group',
				'LdapGroupConnector.status' => LdapConnector::STATUS_ACTIVE
			],
			'order' => [
				'LdapGroupConnector.name' => 'ASC'
			]
		]);

		return $ldapConnectors;
	}

	public function getLdapAuthConnectors()
	{
		$ldapConnectors = $this->LdapAuthConnector->find('list', [
			'conditions' => [
				'LdapAuthConnector.type' => 'authenticator',
				'LdapAuthConnector.status' => LdapConnector::STATUS_ACTIVE
			],
			'order' => [
				'LdapAuthConnector.name' => 'ASC'
			]
		]);

		return $ldapConnectors;
	}

	public function getGroups()
	{
		$groups = $this->Group->find('list', [
			'order' => [
				'Group.name' => 'ASC'
			]
		]);

		return $groups;
	}

	public function getPortals()
	{
		return Portal::portals();
	}

    public static function noUserActionOptions($val = null)
    {
    	$options = [
    		self::NO_USER_ACTION_DISABLE => __('Disable'),
    		self::NO_USER_ACTION_DELETE => __('Delete'),
    		self::NO_USER_ACTION_IGNORE => __('Ignore')
    	];

    	return parent::enum($val, $options);
    }

    public function afterSave($created, $options = [])
    {
    	if ($created) {
    		// Audit trait - sync process has been created
			$this->createSystemLog(self::SYSTEM_LOG_LDAP_SYNC_CREATED)
				->foreignKey($this->data['LdapSynchronization']['id'])
				->message([$this->data['LdapSynchronization']['name']])
				->log();
    	}
    }

    public function beforeDelete($cascade = true)
    {
    	$data = $this->find('first', [
    		'fields' => [
    			'LdapSynchronization.id', 'LdapSynchronization.name'
    		],
    		'conditions' => [
    			'LdapSynchronization.id' => $this->id
    		],
    		'recursive' => -1
    	]);

    	if (!empty($data)) {
    		$this->deletedEntityId = $data['LdapSynchronization']['id'];
    		$this->deletedEntityName = $data['LdapSynchronization']['name'];
    	}

    	$ret = true;
		if ($this->inUse($this->id)) {
			$ret = false;
			$this->customDeleteMessage = __('This sync has accounts associated with it, please remove the accounts and then you will be able to remove this sync.');
		}

		return $ret;
    }

    public function afterDelete()
    {
    	if (!empty($this->deletedEntityId) && !empty($this->deletedEntityName)) {
    		// Audit trait - sync process has been created
			$this->createSystemLog(self::SYSTEM_LOG_LDAP_SYNC_DELETED)
				->foreignKey($this->deletedEntityId)
				->message([$this->deletedEntityName])
				->log();
    	}
    }

    /**
	 * Checks if an Ldap Synchronization is used by any user.
	 */
	public function inUse($id)
	{
		$results = $this->User->find('all', [
			'contain' => [
				'LdapSynchronization'
			]
		]);
		$ldapSyncs = Hash::extract($results, '{n}.LdapSynchronization.{n}.id');

		if (in_array($id, $ldapSyncs)) {
			return true;
		} else {
			return false;
		}
	}
}
