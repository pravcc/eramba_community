<?php
App::uses('AppAudit', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('UserFields', 'UserFields.Lib');
App::uses('Project', 'Model');
App::uses('SecurityIncident', 'Model');
App::uses('SecurityServiceAuditsHelper', 'View/Helper');

// App::uses('AppModel', 'Model');
class SecurityServiceAudit extends AppAudit implements InheritanceInterface {
	public $displayField = 'planned_date';

	protected $auditParentModel = 'SecurityService';

	public $mapping = array(
		'indexController' => array(
			'basic' => 'securityServiceAudits',
			'advanced' => 'securityServiceAudits',
			'params' => array('security_service_id')
		),
		'titleColumn' => 'planned_date',
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
				'audit_metric_description', 'audit_success_criteria', 'result_description' 
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
				'AuditOwner',
				'AuditEvidenceOwner'
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
		'result_description' => array(
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

	public $hasOne = array(
		'SecurityServiceAuditImprovement'
	);

	const RESULT_INCOMPLETE = null;
	const RESULT_FAILED = 0;
	const RESULT_PASSED = 1;

	public static function results($value = null) {
        $options = array(
            self::RESULT_INCOMPLETE => __('Incomplete'),
            self::RESULT_FAILED => __('Failed'),
            self::RESULT_PASSED => __('Passed')
        );
        return parent::enum($value, $options);
    }

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Audits');
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
				'renderHelper' => ['SecurityServiceAudits', 'securityServiceField']
			),
			'audit_metric_description' => array(
				'label' => __('Audit Methodology'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The text above is copied from what the control has defined as Audit Methodology. <br>Any modification to the content of this field in this form will be highlighted once you save the audit.')
			),
			'audit_success_criteria' => array(
				'label' => __('Audit Success Criteria'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The text above is copied from what the control has defined as Audit Criteria. <br>Any modification to the content of this field in this form will be highlighted once you save the audit.')
			),
			'result' => array(
				'label' => __('Audit Result'),
				'options' => array($this, 'getAuditStatuses'),
				'editable' => false,
				'description' => __('Pass: If the result of the audit was succesfull<br>Fail: If the result of the audit was not succesful, the control will be tagged as "Failed Audits"'),
				'dependency' => true
			),
			'result_description' => array(
				'label' => __('Audit Conclusion'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the audit conclusion. You can attach your audit conclusion, evidence, Etc to this audit once you save this record.')
			),
			'AuditOwner' => $UserFields->getFieldDataEntityData($this, 'AuditOwner', [
				'label' => __('Audit Owner'), 
				'description' => __('This role is typically used to record the individual that lead the audit (testing) process'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'AuditEvidenceOwner' => $UserFields->getFieldDataEntityData($this, 'AuditEvidenceOwner', [
				'label' => __('Audit Evidence Owner'), 
				'description' => __('This role is typically used to record and notify the individual that must provide evidence'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'planned_date' => array(
				'label' => __('Planned Start'),
				'editable' => true,
				'description' => __('Select the date when this audit was supposed to begin'),
				// 'hidden' => true
			),
			'start_date' => array(
				'label' => __('Audit Start Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select the date when the audit begun, this might be different from the date it was supposed to start')
			),
			'end_date' => array(
				'label' => __('Audit End Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select the date when the audit was completed.<br>'),
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
				'SECSERV_OWNER' => $UserFields->getNotificationSystemData('SecurityService.ServiceOwner', [
					'name' => __('Security Service Owner')
				]),
				'SECSERV_AUDITMETRIC' => array(
					'field' => 'SecurityServiceAudit.audit_metric_description',
					'name' => __('Security Service Audit Metric Description')
				),
				'SECSERV_AUDITCRITERIA' => array(
					'field' => 'SecurityServiceAudit.audit_success_criteria',
					'name' => __('Security Service Audit Success Criteria')
				),
				'SECSERV_AUDITRESULT' => array(
					'type' => 'callback',
					'field' => 'SecurityServiceAudit.result',
					'name' => __('Security Service Audit Result'),
					'callback' => array($this, 'getFormattedResult')
				),
				'SECSERV_AUDITCONCLUSION' => array(
					'field' => 'SecurityServiceAudit.result_description',
					'name' => __('Security Service Audit Conclusion')
				),
				'SECSERV_AUDITDATE' => array(
					'field' => 'SecurityServiceAudit.planned_date',
					'name' => __('Security Service Audit Date')
				),
				'SECSERV_AUDITOWNER' => $UserFields->getNotificationSystemData('AuditOwner', [
					'name' => __('Security Service Audit Owner')
				]),
			),
			'customEmail' =>  true,
			'associateCustomFields' => array('SecurityService' => 'SECSERV')
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Security Service Audits'),
			'pdf_file_name' => __('security_service_audits'),
			'csv_file_name' => __('security_service_audits'),
			'view_item' => array(
				'ajax_action' => array(
					'controller' => 'securityServiceAudits',
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
				->textField('audit_metric_description', [
					'showDefault' => true
				])
				->textField('audit_success_criteria', [
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
				->textField('result_description', [
					'showDefault' => true
				])
				->userField('AuditOwner', 'AuditOwner', [
					'showDefault' => true
				])
				->userField('AuditEvidenceOwner', 'AuditEvidenceOwner', [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_audit_missing', 'audit_missing')
			->group('SecurityIncident', [
				'name' => __('Security Incident')
			])
				->multipleSelectField('SecurityService-SecurityIncident', [ClassRegistry::init('SecurityIncident'), 'getList'])
				// ->objectStatus('SecurityService-ongoing_incident')
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
				])
			->group('Project', [
				'name' => __('Project Management')
			])
				->multipleSelectField('SecurityService-Project', [ClassRegistry::init('Project'), 'getList'], [
					'label' => __('Project')
				])
				->textField('ProjectAchievement-description', [
					'label' => __('Project Task'),
					'findField' => 'SecurityService.Project.ProjectAchievement.description',
					'returnField' => 'SecurityServiceAudit.security_service_id',
					'fieldData' => 'SecurityService.Project.ProjectAchievement',
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
        	'audit_failed' => [
        		'title' => __('Failed'),
                'callback' => [$this, 'statusAuditFailed'],
                'type' => 'danger',
                'storageSelf' => false
        	],
        	'audit_missing' => [
        		'title' => __('Expired'),
                'callback' => [$this, 'statusAuditMissing'],
                'storageSelf' => false,
                'regularTrigger' => true,
        	],
        	'audits_all_done' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'audits_not_all_done' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'audits_last_passed' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'audits_last_not_passed' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'audits_last_missing' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
            'audits_improvements' => [
                'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true
            ],
        ];
    }

    public function statusAuditFailed() {
		$data = $this->find('count', [
			'conditions' => [
				'SecurityServiceAudit.id' => $this->id,
				'SecurityServiceAudit.result' => false,
			],
			'fields' => [
				'SecurityServiceAudit.id'
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function statusAuditMissing() {
		$data = $this->find('count', [
			'conditions' => [
				'SecurityServiceAudit.id' => $this->id,
				'SecurityServiceAudit.result IS NULL',
				'DATE(SecurityServiceAudit.planned_date) < DATE(NOW())'
			],
			'fields' => [
				'SecurityServiceAudit.id'
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function beforeSave($options = array()) {
		parent::beforeSave($options);

		$ret = true;

		return $ret;
	}

	public function afterSave($created, $options = array()) {
		parent::afterSave($created, $options = array());

		$ret = true;
		
		if ($created && !empty($this->id)) {
			// $ret &= $this->createAuditDate($this->id);

			$this->triggerObjectStatus();
		}
		
		return $ret;
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications']['object_reminder'] = $this->_getModelObjectReminderNotification();

		$config['notifications'] = array_merge($config['notifications'], [
			'security_service_audit_begin_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => -1,
				'label' => __('Scheduled Audit in (-1 day)'),
				'description' => __('Notifies 1 day before a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => -5,
				'label' => __('Scheduled Audit in (-5 days)'),
				'description' => __('Notifies 5 days before a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => -10,
				'label' => __('Scheduled Audit in (-10 days)'),
				'description' => __('Notifies 10 days before a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => -20,
				'label' => __('Scheduled Audit in (-20 days)'),
				'description' => __('Notifies 20 days before a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => -30,
				'label' => __('Scheduled Audit in (-30 days)'),
				'description' => __('Notifies 30 days before a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => 1,
				'label' => __('Scheduled Audit in (+1 day)'),
				'description' => __('Notifies 1 day after a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => 5,
				'label' => __('Scheduled Audit in (+5 days)'),
				'description' => __('Notifies 5 days after a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => 10,
				'label' => __('Scheduled Audit in (+10 days)'),
				'description' => __('Notifies 10 days after a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => 20,
				'label' => __('Scheduled Audit in (+20 days)'),
				'description' => __('Notifies 20 days after a scheduled Security Service Audit begins')
			],
			'security_service_audit_begin_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditBegin',
				'days' => 30,
				'label' => __('Scheduled Audit in (+30 days)'),
				'description' => __('Notifies 30 days after a scheduled Security Service Audit begins')
			],
			'security_service_audit_failed' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditFailed',
				'label' => __('Security Service Audit Failed'),
				'description' => __('Notifies when the result of a Security Service Audit is failed')
			],
			'security_service_audit_passed' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.SecurityServiceAuditPassed',
				'label' => __('Security Service Audit Passed'),
				'description' => __('Notifies when the result of a Security Service Audit is passed')
			],
		]);

		return $config;
	}

	public function getReportsConfig()
	{
		return [
			'finder' => [
				'options' => [
					'contain' => Hash::merge($this->containList(), [
						'SecurityService' => [
							'ComplianceManagement' => [
								'CompliancePackageItem' => [
									'CompliancePackage' => [
										'CompliancePackageRegulator'
									]
								]
							],
							'DataAsset' => [
								'DataAssetInstance' => [
									'Asset'
								]
							]
						]
					])
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
						'SecurityServiceAuditImprovement' => [
							'Project',
							'SecurityIncident'
						],
						'CustomFieldValue',
						'AuditOwner',
						'AuditOwnerGroup',
						'AuditEvidenceOwner',
						'AuditEvidenceOwnerGroup'
					]
				]
			],
			'assoc' => [
				'SecurityService',
			],
			'seed' => [
				[$this, 'customMacros']
			]
		];
	}

	public function customMacros($Collection)
	{
		$groupSettings = $this->getMacroGroupModelSettings();

		$Macro = new Macro($this->getMacroAlias('compliance_items_list'), __('List of Related Compliance Items'), null, ['SecurityServiceAuditsHelper', 'complianceItemsList']);
		$Collection->add($Macro);

		$Macro = new Macro($this->getMacroAlias('risk_items_list'), __('List of Related Risk Items'), null, ['SecurityServiceAuditsHelper', 'riskItemsList']);
		$Collection->add($Macro);

		$Macro = new Macro($this->getMacroAlias('data_flow_items_list'), __('List of Related Data Flow Items'), null, ['SecurityServiceAuditsHelper', 'dataAssetItemsList']);
		$Collection->add($Macro);
	}

	public function setCreateValidation() {
		$this->validate = array_merge($this->validate, $this->createValidate);
	}

	/**
     * Get the parent model name, required for InheritanceInterface class.
     */
    public function parentModel() {
        return $this->auditParentModel;
    }

    public function parentNode($type) {
    	return $this->visualisationParentNode('security_service_id');
    }

    public function createAuditDate($auditId) {
    	$audit = $this->find('first', [
    		'conditions' => [
    			'SecurityServiceAudit.id' => $auditId
			],
			'recursive' => -1
		]);

		if (empty($audit)) {
			return false;
		}

		$date = $audit['SecurityServiceAudit']['planned_date'];
		$data = [
			'security_service_id' => $audit['SecurityServiceAudit']['security_service_id'],
			'day' => date('d', strtotime($audit['SecurityServiceAudit']['planned_date'])),
			'month' => date('m', strtotime($audit['SecurityServiceAudit']['planned_date'])),
		];

		$this->SecurityService->SecurityServiceAuditDate->create();
		return $this->SecurityService->SecurityServiceAuditDate->save($data);
	}

	public function checkEndDate($endDate, $startDate) {
		if (!isset($this->data[$this->name][$startDate])) {
			return true;
		}

		return $this->data[$this->name][$startDate] <= $endDate['end_date'];
	}

	public function getRecordTitle($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'SecurityServiceAudit.id' => $id
			),
			'fields' => array(
				'SecurityServiceAudit.planned_date',
				'SecurityService.name'
			),
			'recursive' => 0,
			'softDelete' => false
		));
		
		$value = "";
		if (!empty($data)) {
			$value = sprintf('%s (%s)', $data['SecurityServiceAudit']['planned_date'], $data['SecurityService']['name']);
		}

		return $value;
	}

	public function getFormattedResult($result) {
		$statuses = getAuditStatuses();

		if (isset($statuses[$result])) {
			return $statuses[$result];
		}

		return false;
	}

	private function logStatusToService() {
		$record = $this->find('first', array(
			'conditions' => array(
				'id' => $this->id
			),
			'fields' => array('result'),
			'recursive' => -1
		));
		//debug($this->data);

		if ($record['SecurityServiceAudit']['result'] != $this->data['SecurityServiceAudit']['result']) {
			$statuses = getAuditStatuses();
			$this->SecurityService->addNoteToLog(__('Audit status changed to %s', $statuses[$this->data['SecurityServiceAudit']['result']]));
			$this->SecurityService->setSystemRecord($this->data['SecurityServiceAudit']['security_service_id'], 2);
		}
	}

	/**
	 * Get audits completion statuses.
	 * @param  int $id   Security Service ID.
	 */
	public function getStatuses($id) {
		$audits = $this->find('count', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id,
				'SecurityServiceAudit.result' => null,
				'SecurityServiceAudit.planned_date <' => date('Y-m-d', strtotime('now'))
			),
			'recursive' => -1
		));

		$all_done = false;
		if (empty($audits)) {
			$all_done = true;
		}

		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->find('first', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id,
				'SecurityServiceAudit.planned_date <=' => $today
			),
			'fields' => array('SecurityServiceAudit.id', 'SecurityServiceAudit.result', 'SecurityServiceAudit.planned_date'),
			'order' => array('SecurityServiceAudit.planned_date' => 'DESC'),
			'recursive' => -1
		));

		$last_passed = false;
		if (empty($audit) ||
			(!empty($audit) && in_array($audit['SecurityServiceAudit']['result'], array(1, null)))) {
			$last_passed = true;
		}
		elseif (!empty($audit)) {
			$this->SecurityService->lastAuditFailed = $audit['SecurityServiceAudit']['planned_date'];
		}

		$improvements = false;
		$hasProjects = $this->SecurityService->getAssignedProjects($id);
		if ($hasProjects) {
			$improvements = true;
		}
		else {
			$audit = $this->find('first', array(
				'conditions' => array(
					'SecurityServiceAudit.security_service_id' => $id,
					'SecurityServiceAudit.planned_date <=' => $today,
					'SecurityServiceAudit.result' => array(1, 0)
				),
				'fields' => array('SecurityServiceAudit.id', 'SecurityServiceAudit.result', 'SecurityServiceAuditImprovement.id'),
				'order' => array('SecurityServiceAudit.planned_date' => 'DESC'),
				'contain' => array(
					'SecurityServiceAuditImprovement'
				)
			));

			if (isset($audit['SecurityServiceAuditImprovement']['id']) && $audit['SecurityServiceAuditImprovement']['id'] != null) {
				$improvements = true;
			}
		}

		$audit = $this->find('first', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id,
				'SecurityServiceAudit.planned_date <' => $today
			),
			'fields' => array('SecurityServiceAudit.id', 'SecurityServiceAudit.result', 'SecurityServiceAudit.planned_date'),
			'order' => array('SecurityServiceAudit.planned_date' => 'DESC'),
			'recursive' => -1
		));

		$lastMissing = false;
		if (!empty($audit) && $audit['SecurityServiceAudit']['result'] == null) {
			$this->SecurityService->lastAuditMissing = $audit['SecurityServiceAudit']['planned_date'];
			$lastMissing = true;
		}

		$arr = array(
			'audits_all_done' => (string) (int) $all_done,
			'audits_last_missing' => (string) (int) $lastMissing,
			'audits_last_passed' => (string) (int) $last_passed,
			'audits_improvements' => (string) (int) $improvements,
			'audits_status' => $this->auditStatus($id)
		);

		return $arr;
	}

	private function auditStatus($id = null) {
		$data = $this->SecurityService->find('first', array(
			'conditions' => array(
				'SecurityService.id' => $id
			),
			'fields' => array('id', 'security_service_type_id'),
			'recursive' => -1
		));

		if (empty($data)) {
			return 0;
		}

		if ($data['SecurityService']['security_service_type_id'] == SECURITY_SERVICE_RETIRED) {
			return 2;
		}

		if ($data['SecurityService']['security_service_type_id'] != SECURITY_SERVICE_PRODUCTION) {
			return 1;
		}

		return 0;
	}

	public function logMissingAudits() {
		$yesterday = CakeTime::format('Y-m-d', CakeTime::fromString('-1 day'));

		$audits = $this->find('all', array(
			'conditions' => array(
				'SecurityServiceAudit.planned_date' => $yesterday
			),
			'fields' => array(
				'SecurityServiceAudit.id',
				'SecurityServiceAudit.result',
				'SecurityServiceAudit.planned_date',
				'SecurityServiceAudit.security_service_id'
			),
			'order' => array('SecurityServiceAudit.planned_date' => 'DESC'),
			'recursive' => -1
		));

		foreach ($audits as $item) {
			$msg = __('Last audit missing (%s)', $item['SecurityServiceAudit']['planned_date']);

			if ($item['SecurityServiceAudit']['result'] == null) {
				$securityServiceId = $item['SecurityServiceAudit']['security_service_id'];

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

	public function getProjects() {
		return $this->SecurityService->getProjects();
	}

	public function getProjectStatuses() {
		return $this->SecurityService->Project->getStatuses();
	}

	public function getSecurityIncident() {
		return $this->SecurityService->SecurityIncident->find('list', array(
			'fields' => array('SecurityIncident.id', 'SecurityIncident.title'),
			'order' => array('SecurityIncident.title' => 'ASC')
		));
	}

	public function getSecurityIncidentStatuses() {
		return $this->SecurityService->SecurityIncident->getStatuses();
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

	public function findBySecurityService($data = array(), $filterParams = array()) {
		return $this->SecurityService->findByHabtm($data, $filterParams);
	}

	public function findBySecurityServiceComplex($data = array(), $filterParams = array()) {
		return $this->SecurityService->findComplexType($data, $filterParams);
	}

	public function getSecurityServiceRelatedData($fieldData = array()) {
		return $this->SecurityService->getFilterRelatedData($fieldData);
	}

	public function timeline($id)
	{
		$rawData = $this->find('all', [
			'conditions' => [
				'SecurityServiceAudit.security_service_id' => $id
			],
			'fields' => [
				'SecurityServiceAudit.planned_date',
				'SecurityServiceAudit.result',
			],
			'recursive' => -1
		]);

		$data = [];

		foreach ($rawData as $item) {
			$x = date('Y-m', strtotime($item['SecurityServiceAudit']['planned_date']));
			$y = $item['SecurityServiceAudit']['result'];

			if (isset($data[$x][$y])) {
				$data[$x][$y]++;
			}
			else {
				$data[$x][$y] = 1;
			}
		}

		return $data;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
