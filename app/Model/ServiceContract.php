<?php
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');

class ServiceContract extends AppModel
{
	public $displayField = 'name';
	
	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description', 'third_party_id', 'value', 'start', 'end'
			)
		),
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => ['Owner']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'Owner' => array(
			'minCount' => array(
				'rule' => array('multiple', array('min' => 1)),
				'message' => 'You have to select at least one Owner'
			)
		),
		'third_party_id' => array(
			'rule' => 'notBlank',
			'required' => true,
		),
		'value' => array(
			'numeric' => [
				'rule' => 'numeric',
				'message' => 'This field can only contain numeric values'
			]
		),
		'start' => array(
			'rule' => 'date',
			'required' => true
		),
		'end' => array(
			'rule' => 'date',
			'required' => true
		)
	);

	public $belongsTo = array(
		'ThirdParty' => array(
			'counterCache' => true
		)
	);

	public $hasMany = array(
	);

	public $hasAndBelongsToMany = array(
		'SecurityService',
		'Owner' => [
			'className' => 'User',
			'joinTable' => 'service_contracts_owners',
			'foreignKey' => 'service_contract_id',
			'associationForeignKey' => 'owner_id'
		]
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Contracts');
		$this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Give a name to the contract you have in between this provider and your organization. Examples: Firewall Hardware Support, Firewall Consulting Time, Etc.')
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Describe the scope of this support contract')
			],
			'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'), 
				'description' => __('Select one or more user accounts that are related to this contract.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'third_party_id' => [
				'label' => __('Support Contract Supplier'),
				'editable' => true,
				'description' => __('OPTIONAL: Select one or more third party (Organisatino / Third Party) that delivers this support contract'),
				'quickAdd' => true,
				'inlineEdit' => true,
				'macro' => [
					'name' => 'third_party'
				]
			],
			'value' => [
				'label' => __('Service Value'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The value of contract.')
			],
			'start' => [
				'label' => __('Start Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('When the contract starts')
			],
			'end' => [
				'label' => __('End Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('When the contract finishes')
			],
			'expired' => [
				'label' => __('Expired'),
				'editable' => false,
				'hidden' => true
			],
			'SecurityService' => [
				'label' => __('Security Service'),
				'editable' => false,
			],
		];

		$this->notificationSystem = array(
			'macros' => array(
				'SECSERV_ID' => array(
					'field' => 'SecurityService.{n}.id',
					'name' => __('Security Service ID')
				),
				'SECSERV_NAME' => array(
					'field' => 'SecurityService.{n}.name',
					'name' => __('Security Service Name')
				),
				'SECSERV_OBJECTIVE' => array(
					'field' => 'SecurityService.{n}.objective',
					'name' => __('Security Service Objective')
				),
				'SECSERV_OWNER' => $UserFields->getNotificationSystemData('Owner', [
					'name' => __('Security Service Owner')
				]),
				'SECCONTRACT_NAME' => array(
					'field' => 'ServiceContract.name',
					'name' => __('Security Contract Name')
				),
				'SECCONTRACT_THIRDPARTY' => array(
					'field' => 'ThirdParty.name',
					'name' => __('Third Party Name')
				),
				'SECCONTRACT_VALUE' => array(
					'field' => 'ServiceContract.value',
					'name' => __('Security Contract Value')
				),
				'SECCONTRACT_STARTDATE' => array(
					'field' => 'ServiceContract.start',
					'name' => __('Security Contract Start')
				),
				'SECCONTRACT_ENDDATE' => array(
					'field' => 'ServiceContract.end',
					'name' => __('Security Contract End')
				)
			),
			'customEmail' =>  true
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
				->userField('Owner', 'Owner', [
					'showDefault' => true
				])
				->textField('value', [
					'showDefault' => true
				])
				->dateField('start', [
					'showDefault' => true
				])
				->dateField('end', [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_expired', 'expired')
			->group('ThirdParty', [
				'name' => __('Third Party')
			])
				->multipleSelectField('third_party_id', [ClassRegistry::init('ThirdParty'), 'getList'], [
					'label' => __('Third Party'),
					'showDefault' => true,
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
			->group('SupportContract', [
				'name' => __('Support Contract')
			])
				->multipleSelectField('ServiceContract', [$this, 'getList'], [
					'label' => __('Support Contract')
				]);

		return $advancedFilterConfig;
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'ThirdParty'
			]
		];
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications'] = array_merge($config['notifications'], [
			'service_contract_expiration_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => -1,
				'label' => __('Support Contract Deadline (-1 day)'),
				'description' => __('Notifies 1 day before a Support Contract expires')
			],
			'service_contract_expiration_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => -5,
				'label' => __('Support Contract Deadline (-5 days)'),
				'description' => __('Notifies 5 days before a Support Contract expires')
			],
			'service_contract_expiration_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => -10,
				'label' => __('Support Contract Deadline (-10 days)'),
				'description' => __('Notifies 10 days before a Support Contract expires')
			],
			'service_contract_expiration_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => -20,
				'label' => __('Support Contract Deadline (-20 days)'),
				'description' => __('Notifies 20 days before a Support Contract expires')
			],
			'service_contract_expiration_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => -30,
				'label' => __('Support Contract Deadline (-30 days)'),
				'description' => __('Notifies 30 days before a Support Contract expires')
			],
			'service_contract_expiration_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => 1,
				'label' => __('Support Contract Deadline (+1 day)'),
				'description' => __('Notifies 1 day after a Support Contract expires')
			],
			'service_contract_expiration_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => 5,
				'label' => __('Support Contract Deadline (+5 days)'),
				'description' => __('Notifies 5 days after a Support Contract expires')
			],
			'service_contract_expiration_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => 10,
				'label' => __('Support Contract Deadline (+10 days)'),
				'description' => __('Notifies 10 days after a Support Contract expires')
			],
			'service_contract_expiration_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => 20,
				'label' => __('Support Contract Deadline (+20 days)'),
				'description' => __('Notifies 20 days after a Support Contract expires')
			],
			'service_contract_expiration_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ServiceContractExpiration',
				'days' => 30,
				'label' => __('Support Contract Deadline (+30 days)'),
				'description' => __('Notifies 30 days after a Support Contract expires')
			]
		]);

		return $config;
	}

	public function getObjectStatusConfig() {
        return [
            'expired' => [
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
            'ServiceContract.end < DATE(NOW())'
        ]);
    }

    /**
	 * @deprecated status, in favor of ServiceContract::statusExpired()
	 */
	public function statusIsExpired($id) {
		$today = date('Y-m-d', strtotime('now'));

		$isExpired = $this->find('count', array(
			'conditions' => array(
				'ServiceContract.id' => $id,
				'DATE(ServiceContract.end) <' => $today
			),
			'recursive' => -1
		));

		return $isExpired;
	}

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
			],
		];
	}

	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'end');
	}

	public function getSecurityServices() {
		$data = $this->SecurityService->find('list', array(
			'order' => array('SecurityService.name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getThirdParties() {
		$data = $this->ThirdParty->find('list', array(
			'order' => array('ThirdParty.name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function hasSectionIndex()
	{
		return true;
	}
	
}
