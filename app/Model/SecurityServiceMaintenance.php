<?php
App::uses('AppModel', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('UserFields', 'UserFields.Lib');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class SecurityServiceMaintenance extends AppModel implements InheritanceInterface
{
	public $displayField = 'planned_date';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'indexController' => array(
			'basic' => 'securityServiceMaintenances',
			'advanced' => 'securityServiceMaintenances',
			'params' => array('security_service_id')
		),
		'titleColumn' => false,
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable' => array(
			'ignore' => array(
				'created',
				'modified',
			)
		),
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'task', 'task_conclusion'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'NotificationSystem.NotificationSystem',
				'Reports.Report',
			]
		],
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => [
				'MaintenanceOwner'
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'parentField' => 'security_service_id'
		],
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'start_date' => array(
			'date' => array(
				'rule' => array('date', 'ymd'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'update',
				'message' => 'Enter a valid date.'
			),
		),
		'end_date' => array(
			'date' => array(
				'rule' => array('date', 'ymd'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'update',
				'message' => 'Enter a valid date.'
			),
			'afterStartDate' => array(
				'rule' => array('checkEndDate', 'start_date'),
				'message' => 'End date must happen after the start date.'
			)
		),
		'task_conclusion' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'update',
				'message' => 'This field is required'
			),
		),
	);

	public $createValidate = [
		'planned_date' => [
			'date' => [
				'rule' => ['date', 'ymd'],
				'required' => true,
				'message' => 'Enter a valid date.'
			],
		],
	];

	public $belongsTo = array(
		'SecurityService'
	);

	public $hasMany = array(
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Maintenances');
        $this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = array(
			'security_service_id' => array(
				// 'type' => 'hidden',
				'label' => __('Internal Control'),
				'editable' => true,
				'macro' => [
					'name' => 'security_service'
				],
				'empty' => __('Choose one ...'),
				'renderHelper' => ['SecurityServiceMaintenances', 'securityServiceField']
				// 'hidden' => true
			),
			'task' => array(
				'label' => __('Maintenance Task'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describes the task performed, this is a copy from what is defined on the control')
			),
			'task_conclusion' => array(
				'label' => __('Task Conclusion'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describes the result of the task')
			),
			'MaintenanceOwner' => $UserFields->getFieldDataEntityData($this, 'MaintenanceOwner', [
				'label' => __('Maintenance Owner'), 
				'description' => __('Who executed the task?'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'planned_date' => array(
				'label' => __('Planned Start'),
				'editable' => true,
				'description' => __('Select the date when the mantainance was supposed to begin')
				// 'hidden' => true
			),
			'start_date' => array(
				'label' => __('Maintenance Start Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select the date when the mantainance begun')
			),
			'end_date' => array(
				'label' => __('Maintenance End Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select the date when the mantainance was completed')
			),
			'result' => array(
				'label' => __('Task Result'),
				'options' => array($this, 'getAuditStatuses'),
				'editable' => false,
				'description' => __('Describe the result of the task'),
				'dependency' => true
			),
			'attachment' => array(
				'label' => __('Evidence'),
				'description' => __('Drop one or more attachments that have been used in this audit (evidence, analysis, etc)'),
				'editable' => true,
				'usable' => false,
				'renderHelper' => ['Attachments.Attachments', 'attachmentField']
			),
		);

		$this->notificationSystem = array(
			'macros' => array(
				'SECSERV_ID' => array(
					'field' => 'SecurityService.id',
					'name' => __('Security Service ID')
				),
				'SECSERV_NAME' => array(
					'field' => 'SecurityService.name',
					'name' => __('Security Service Name')
				),
				'SECSERV_OBJECTIVE' => array(
					'field' => 'SecurityService.objective',
					'name' => __('Security Service Objective')
				),
				'SECSERV_OWNER' => array(
					'field' => 'SecurityService.User.full_name',
					'name' => __('Security Service Owner')
				),
				'SECSERV_MAINTENANCETASK' => array(
					'field' => 'SecurityServiceMaintenance.task',
					'name' => __('Security Service Maintenance Task')
				),
				'SECSERV_MAINTENANCECONCLUSION' => array(
					'field' => 'SecurityServiceMaintenance.task_conclusion',
					'name' => __('Security Service Maintenance Conclusion')
				),
				'SECSERV_MAINTENANCERESULT' => array(
					'type' => 'callback',
					'field' => 'SecurityServiceMaintenance.result',
					'name' => __('Security Service Maintenance Result'),
					'callback' => array($this, 'getFormattedResult')
				),
				'SECSERV_MAINTENANCEDATE' => array(
					'field' => 'SecurityServiceMaintenance.planned_date',
					'name' => __('Security Service Maintenance Date')
				),
				'SECSERV_MAINTENANCEOWNER' => array(
					'field' => 'User.full_name',
					'name' => __('Security Service Maintenance Owner')
				)
			),
			'customEmail' =>  true,
			'associateCustomFields' => array('SecurityService' => 'SECSERV')
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Security Service Maintenances'),
			'pdf_file_name' => __('security_service_maintenances'),
			'csv_file_name' => __('security_service_maintenances'),
			'view_item' => array(
				'ajax_action' => array(
					'controller' => 'securityServiceMaintenances',
					'action' => 'index'
				)
			),
			'bulk_actions' => true,
			'history' => true,
			'trash' => true,
			'use_new_filters' => true
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
				->multipleSelectField('security_service_id', [ClassRegistry::init('SecurityService'), 'getList'], [
					'showDefault' => true
				])
				->textField('task', [
					'showDefault' => true
				])
				->dateField('planned_date', [
					'showDefault' => true
				])
				->dateField('start_date', [
					'showDefault' => true
				])
				->dateField('end_date', [
					'showDefault' => true
				])
				->selectField('result', [$this, 'getAuditStatusesAll'], [
					'showDefault' => true
				])
				->textField('task_conclusion', [
					'showDefault' => true
				])
				->userField('MaintenanceOwner', 'MaintenanceOwner', [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_maintenance_missing', 'maintenance_missing')
			->group('ComplianceManagement', [
				'name' => __('Compliance Analysis')
			])
				->multipleSelectField('CompliancePackage-compliance_package_regulator_id', [ClassRegistry::init('CompliancePackageRegulator'), 'getList'], [
					'label' => __('Compliance Package'),
					'findField' => 'SecurityService.ComplianceManagement.CompliancePackageItem.CompliancePackage.compliance_package_regulator_id',
					'fieldData' => 'SecurityService.ComplianceManagement.CompliancePackageItem.CompliancePackage.compliance_package_regulator_id',
				])
				->textField('CompliancePackage-package_id', [
					'label' => __('Requirement Chapter Number'),
					'findField' => 'SecurityService.ComplianceManagement.CompliancePackageItem.CompliancePackage.package_id',
					'fieldData' => 'SecurityService.ComplianceManagement.CompliancePackageItem.CompliancePackage.package_id',
				])
				->textField('CompliancePackage-name', [
					'label' => __('Requirement Chapter Title'),
					'findField' => 'SecurityService.ComplianceManagement.CompliancePackageItem.CompliancePackage.name',
					'fieldData' => 'SecurityService.ComplianceManagement.CompliancePackageItem.CompliancePackage.name',
				])
				->textField('CompliancePackageItem-item_id', [
					'label' => __('Requirement Item Number'),
					'findField' => 'SecurityService.ComplianceManagement.CompliancePackageItem.item_id',
					'fieldData' => 'SecurityService.ComplianceManagement.CompliancePackageItem.item_id',
				])
				->textField('CompliancePackageItem-name', [
					'label' => __('Requirement Item Title'),
					'findField' => 'SecurityService.ComplianceManagement.CompliancePackageItem.name',
					'fieldData' => 'SecurityService.ComplianceManagement.CompliancePackageItem.name',
				])
			->group('Risk', [
				'name' => __('Asset Risk')
			])
				->multipleSelectField('SecurityService-Risk', [ClassRegistry::init('Risk'), 'getList'], [
					'label' => __('Asset Risk'),
				])
			->group('ThirdPartyRisk', [
				'name' => __('Third Party Risk')
			])
				->multipleSelectField('SecurityService-ThirdPartyRisk', [ClassRegistry::init('ThirdPartyRisk'), 'getList'], [
					'label' => __('Third Party Risk')
				])
			->group('BusinessContinuity', [
				'name' => __('Business Impact Analysis')
			])
				->multipleSelectField('SecurityService-BusinessContinuity', [ClassRegistry::init('BusinessContinuity'), 'getList'], [
					'label' => __('Business Impact Analysis')
				])
			->group('DataAsset', [
				'name' => __('Data Flow Analysis')
			])
				->multipleSelectField('DataAssetInstance-asset_id', [ClassRegistry::init('Asset'), 'getList'], [
					'label' => __('Asset'),
					'findField' => 'SecurityService.DataAsset.DataAssetInstance.asset_id',
					'fieldData' => 'SecurityService.DataAsset.DataAssetInstance.asset_id'
				])
				->multipleSelectField('SecurityService-DataAsset', [ClassRegistry::init('Risk'), 'getList'], [
					'label' => __('Data Asset Flow')
				])
				->multipleSelectField('DataAsset-data_asset_status_id', [ClassRegistry::init('DataAsset'), 'statuses'], [
					'label' => __('Data Asset Flow Type'),
					'findField' => 'SecurityService.DataAsset.data_asset_status_id',
					'fieldData' => 'SecurityService.DataAsset.data_asset_status_id'
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getDisplayFilterFields()
    {
        return ['security_service_id', 'planned_date'];
    }

	public function getObjectStatusConfig() {
        return [
        	'maintenance_failed' => [
        		'title' => __('Failed'),
                'callback' => [$this, 'statusMaintenanceFailed'],
                'type' => 'danger',
                'storageSelf' => false
        	],
        	'maintenance_missing' => [
        		'title' => __('Expired'),
                'callback' => [$this, 'statusMaintenanceMissing'],
                'storageSelf' => false,
                'regularTrigger' => true,
        	],
        	'maintenances_all_done' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'maintenances_not_all_done' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'maintenances_last_passed' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'maintenances_last_not_passed' => [
            	'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'maintenances_last_missing' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
        ];
    }

    public function statusMaintenanceFailed() {
		$data = $this->find('count', [
			'conditions' => [
				'SecurityServiceMaintenance.id' => $this->id,
				'SecurityServiceMaintenance.result' => false,
			],
			'fields' => [
				'SecurityServiceMaintenance.id'
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function statusMaintenanceMissing() {
		$data = $this->find('count', [
			'conditions' => [
				'SecurityServiceMaintenance.id' => $this->id,
				'SecurityServiceMaintenance.result IS NULL',
				'DATE(SecurityServiceMaintenance.planned_date) < DATE(NOW())'
			],
			'fields' => [
				'SecurityServiceMaintenance.id'
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function getReportsConfig()
	{
		return [
			'finder' => [
				'options' => [
					'contain' => [
						'SecurityService' => [
							'SecurityServiceType',
							'ServiceClassification',
							'Classification',
							'SystemRecord',
							'Issue',
							'SecurityServiceAudit',
							'SecurityServiceAuditDate',
							'SecurityServiceMaintenance',
							'SecurityServiceMaintenanceDate',
							'CustomFieldValue',
							'SecurityIncident',
							'DataAsset' => [
								'DataAssetInstance' => [
									'Asset'
								]
							],
							'Projects',
							'ServiceContract',
							'SecurityPolicy',
							'Risk',
							'ThirdPartyRisk',
							'BusinessContinuity',
							'ComplianceManagement' => [
								'CompliancePackageItem' => [
									'CompliancePackage' => [
										'CompliancePackageRegulator'
									]
								]
							],
							'Project',
							'ServiceOwner',
							'ServiceOwnerGroup',
							'Collaborator',
							'CollaboratorGroup',
							'AuditOwner',
							'AuditOwnerGroup',
							'AuditEvidenceOwner',
							'AuditEvidenceOwnerGroup',
							'MaintenanceOwner',
							'MaintenanceOwnerGroup'
						],
						'CustomFieldValue',
						'MaintenanceOwner',
						'MaintenanceOwnerGroup',
					]
				]
			],
			'table' => [
				'model' => [
					'SecurityService'
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'SecurityService',
			],
		];
	}

	/**
     * Get the parent model name, required for InheritanceInterface class.
     */
    public function parentModel() {
        return 'SecurityService';
    }

    public function parentNode($type) {
    	return $this->visualisationParentNode('security_service_id');
    }

	public function checkEndDate($endDate, $startDate) {
		if (!isset($this->data[$this->name][$startDate])) {
			return true;
		}

		return $this->data[$this->name][$startDate] <= $endDate['end_date'];
	}

	public function beforeDelete($cascade = true) {
		$ret = true;

		return $ret;
	}

	public function afterDelete() {
		$ret = true;

		return $ret;
	}

	public function beforeSave($options = array()) {
		parent::beforeSave($options);

		$ret = true;

		$ret &= $this->SecurityService->saveMaintenances($this->parentNodeId(), 'before');
		return $ret;
	}

	public function afterSave($created, $options = array()) {
		$ret = $this->SecurityService->saveMaintenances($this->parentNodeId(), 'after');

		if ($created && !empty($this->id)) {
			// $ret &= $this->createMaintenanceDate($this->id);

			$this->triggerObjectStatus();
		}

		return $ret;
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications']['object_reminder'] = $this->_getModelObjectReminderNotification();
		
		$config['notifications'] = array_merge($config['notifications'], [
			'security_service_maintenance_begin_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => -1,
				'label' => __('Scheduled Maintenance in (-1 day)'),
				'description' => __('Notifies 1 day before a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => -5,
				'label' => __('Scheduled Maintenance in (-5 days)'),
				'description' => __('Notifies 5 days before a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => -10,
				'label' => __('Scheduled Maintenance in (-10 days)'),
				'description' => __('Notifies 10 days before a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => -20,
				'label' => __('Scheduled Maintenance in (-20 days)'),
				'description' => __('Notifies 20 days before a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => -30,
				'label' => __('Scheduled Maintenance in (-30 days)'),
				'description' => __('Notifies 30 days before a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => 1,
				'label' => __('Scheduled Maintenance in (+1 day)'),
				'description' => __('Notifies 1 day after a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => 5,
				'label' => __('Scheduled Maintenance in (+5 days)'),
				'description' => __('Notifies 5 days after a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => 10,
				'label' => __('Scheduled Maintenance in (+10 days)'),
				'description' => __('Notifies 10 days after a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => 20,
				'label' => __('Scheduled Maintenance in (+20 days)'),
				'description' => __('Notifies 20 days after a scheduled Security Service Maintenance begins')
			],
			'security_service_maintenance_begin_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceMaintenanceBegin',
				'days' => 30,
				'label' => __('Scheduled Maintenance in (+30 days)'),
				'description' => __('Notifies 30 days after a scheduled Security Service Maintenance begins')
			]
		]);

		return $config;
	}

	public function setCreateValidation() {
		$this->validate = array_merge($this->validate, $this->createValidate);
	}

	public function createMaintenanceDate($maintenanceId) {
		$maintenance = $this->find('first', [
    		'conditions' => [
    			'SecurityServiceMaintenance.id' => $maintenanceId
			],
			'recursive' => -1
		]);

		if (empty($maintenance)) {
			return false;
		}

		$date = $maintenance['SecurityServiceMaintenance']['planned_date'];
		$data = [
			'security_service_id' => $maintenance['SecurityServiceMaintenance']['security_service_id'],
			'day' => date('d', strtotime($maintenance['SecurityServiceMaintenance']['planned_date'])),
			'month' => date('m', strtotime($maintenance['SecurityServiceMaintenance']['planned_date'])),
		];

		$this->SecurityService->SecurityServiceMaintenanceDate->create();
		return $this->SecurityService->SecurityServiceMaintenanceDate->save($data);
	}

	public function getRecordTitle($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'SecurityServiceMaintenance.id' => $id
			),
			'fields' => array(
				'SecurityServiceMaintenance.planned_date',
				'SecurityService.name'
			),
			'recursive' => 0
		));

		$value = "";
		if (!empty($data)) {
			$value = sprintf('%s (%s)', $data['SecurityServiceMaintenance']['planned_date'], $data['SecurityService']['name']);
		}

		return $value;
	}

	private function getMaintenance() {
		$maintenance = $this->find('first', array(
			'conditions' => array(
				'id' => $this->id
			),
			'recursive' => -1
		));

		return $maintenance;
	}

	private function logStatusToService() {
		$record = $this->find('first', array(
			'conditions' => array(
				'id' => $this->id
			),
			'fields' => array('result'),
			'recursive' => -1
		));

		if ($record['SecurityServiceMaintenance']['result'] != $this->data['SecurityServiceMaintenance']['result']) {
			$statuses = getAuditStatuses();
			$this->SecurityService->addNoteToLog(__('Maintenance status changed to %s', $statuses[$this->data['SecurityServiceMaintenance']['result']]));
			$this->SecurityService->setSystemRecord($this->data['SecurityServiceMaintenance']['security_service_id'], 2);
		}
	}

	/**
	 * Get maintenance completion.
	 * @param  int $id   Security Service ID.
	 * @return array     Result.
	 */
	public function getStatuses($id = null) {
		$maintenances = $this->find('count', array(
			'conditions' => array(
				'SecurityServiceMaintenance.security_service_id' => $id,
				'SecurityServiceMaintenance.result' => null,
				'SecurityServiceMaintenance.planned_date <' => date('Y-m-d', strtotime('now'))
			),
			'recursive' => -1
		));

		$all_done = false;
		if (empty($maintenances)) {
			$all_done = true;
		}

		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$maintenances = $this->find('all', array(
			'conditions' => array(
				'SecurityServiceMaintenance.security_service_id' => $id,
				'SecurityServiceMaintenance.planned_date <=' => $today
			),
			'fields' => array('SecurityServiceMaintenance.id', 'SecurityServiceMaintenance.result'),
			'order' => array('SecurityServiceMaintenance.planned_date' => 'DESC'),
			'recursive' => -1,
			'limit' => 1
		));

		$maintenance = $this->find('first', array(
			'conditions' => array(
				'SecurityServiceMaintenance.security_service_id' => $id,
				'SecurityServiceMaintenance.planned_date <=' => $today
			),
			'fields' => array('SecurityServiceMaintenance.id', 'SecurityServiceMaintenance.result'),
			'order' => array('SecurityServiceMaintenance.planned_date' => 'DESC'),
			'recursive' => -1
		));

		$last_passed = false;
		if (empty($maintenance) ||
			(!empty($maintenance) && in_array($maintenance['SecurityServiceMaintenance']['result'], array(1, null)))) {
			$last_passed = true;
		}

		$maintenance = $this->find('first', array(
			'conditions' => array(
				'SecurityServiceMaintenance.security_service_id' => $id,
				'SecurityServiceMaintenance.planned_date <' => $today
			),
			'fields' => array('SecurityServiceMaintenance.id', 'SecurityServiceMaintenance.result', 'SecurityServiceMaintenance.planned_date'),
			'order' => array('SecurityServiceMaintenance.planned_date' => 'DESC'),
			'recursive' => -1
		));

		$lastMissing = false;
		if (!empty($maintenance) && $maintenance['SecurityServiceMaintenance']['result'] == null) {
			$this->SecurityService->lastMaintenanceMissing = $maintenance['SecurityServiceMaintenance']['planned_date'];
			$lastMissing = true;
		}

		return array(
			'maintenances_all_done' => (string) (int) $all_done,
			'maintenances_last_missing' => (string) (int) $lastMissing,
			'maintenances_last_passed' => (string) (int) $last_passed
		);
	}

	public function logMissingMaintenances() {
		$yesterday = CakeTime::format('Y-m-d', CakeTime::fromString('-1 day'));

		$audits = $this->find('all', array(
			'conditions' => array(
				'SecurityServiceMaintenance.planned_date' => $yesterday
			),
			'fields' => array(
				'SecurityServiceMaintenance.id',
				'SecurityServiceMaintenance.result',
				'SecurityServiceMaintenance.planned_date',
				'SecurityServiceMaintenance.security_service_id'
			),
			'order' => array('SecurityServiceMaintenance.planned_date' => 'DESC'),
			'recursive' => -1
		));

		foreach ($audits as $item) {
			$msg = __('Last maintenance missing (%s)', $item['SecurityServiceMaintenance']['planned_date']);

			if ($item['SecurityServiceMaintenance']['result'] == null) {
				$securityServiceId = $item['SecurityServiceMaintenance']['security_service_id'];

				$this->SecurityService->id = $securityServiceId;
				$this->SecurityService->addNoteToLog($msg);
				$this->SecurityService->setSystemRecord($securityServiceId, 2);
			}
		}
	}

	public function getAuditStatuses() {
		return getAuditStatuses();
	}

	public function getAuditStatusesAll() {
		$statuses = am(getAuditStatuses(), [
			AbstractQuery::NULL_VALUE => __('Incomplete')
		]);

		return $statuses;
	}

	public function findByClassifications($data = array()) {
		return $this->SecurityService->findByClassifications($data);
	}

	public function getClassifications() {
		return $this->SecurityService->getClassifications();
	}

	public function getThirdParties() {
		return $this->SecurityService->getThirdParties();
	}

	public function findByCompliancePackage($data = array()) {
		return $this->SecurityService->findByCompliancePackage($data);
	}

	public function findBySecurityService($data = array(), $filterParams = array()) {
		return $this->SecurityService->findByHabtm($data, $filterParams);
	}

	public function getSecurityServiceRelatedData($fieldData = array()) {
		return $this->SecurityService->getFilterRelatedData($fieldData);
	}

	public function getFormattedResult($result) {
		$statuses = getAuditStatuses();

		if (isset($statuses[$result])) {
			return $statuses[$result];
		}

		return false;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
