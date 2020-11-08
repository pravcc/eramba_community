<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('AppIndexCrudAction', 'Controller/Crud/Action');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('Hash', 'Utility');
App::uses('UserFields', 'UserFields.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('SecurityServiceItemData', 'Model/FielDData/Item');
App::uses('SecurityServiceItemCollection', 'Model/FielDData/Collection');
App::uses('Macro', 'Macros.Lib');
App::uses('SecurityServicesHelper', 'View/Helper');

class SecurityService extends AppModel
{
	public $displayField = 'name';

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

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable' => array(
			'ignore' => array(
				'security_incident_open_count',
				'created',
				'modified',
			)
		),
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'objective', 'documentation_url', 'security_service_type_id', 'service_classification_id', 'audit_metric_description', 'audit_success_criteria', 'maintenance_metric_description', 'opex', 'capex', 'resource_utilization'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
                'Reports.Report',
			]
		],
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => [
				'ServiceOwner' => [
					'mandatory' => false
				],
				'Collaborator' => [
					'mandatory' => false
				],
				'AuditOwner' => [
					'customRolesInit' => false
				],
				'AuditEvidenceOwner' => [
					'customRolesInit' => false
				],
				'MaintenanceOwner' => [
					'customRolesInit' => false
				]
			]
		],
		'AssociativeDelete.AssociativeDelete' => [
			'associations' => ['SecurityServiceAudit', 'SecurityServiceMaintenance']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'ImportTool.ImportTool',
		'SubSection' => [
			'childModels' => true
		],
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'documentation_url' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => true,
				'Please enter a valid URL'
			),
			'url' => array(
				'rule' => array('url', true),
				'allowEmpty' => true,
				'message' => 'Please enter a valid URL'
			)
		),
		'audit_metric_description' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		'audit_success_criteria' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		'maintenance_metric_description' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		),
		'opex' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			),
			'number' => [
				'rule' => 'numeric',
				'message' => 'This field has to be a number'
			]
		],
		'capex' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			),
			'number' => [
				'rule' => 'numeric',
				'message' => 'This field has to be a number'
			]
		],
		'resource_utilization' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			),
			'number' => [
				'rule' => 'numeric',
				'message' => 'This field has to be a number'
			]
		]
	);

	public $belongsTo = array(
		'SecurityServiceType',
		'ServiceClassification'
	);

	public $hasMany = array(
		'SecurityServiceAudit',
		'SecurityServiceAuditDate',
		'SecurityServiceMaintenance',
		'SecurityServiceMaintenanceDate',
		'Classification' => array(
			'className' => 'SecurityServiceClassification'
		),
		'SystemRecord' => array(
			'className' => 'SystemRecord',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'SystemRecord.model' => 'SecurityService'
			)
		),
		'Issue' => array(
			'className' => 'Issue',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Issue.model' => 'SecurityService'
			)
		),
		'SecurityServiceIssue' => array(
			'className' => 'SecurityServiceIssue',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'SecurityServiceIssue.model' => 'SecurityService'
			)
		)
	);

	public $hasAndBelongsToMany = array(
		'ServiceContract',
		'SecurityPolicy',
		'Risk',
		'ThirdPartyRisk',
		'BusinessContinuity',
		'SecurityIncident' => array(
			'className' => 'SecurityIncident',
			'with' => 'SecurityIncidentsSecurityService'
		),
		'DataAsset' => array(
			'with' => 'DataAssetsSecurityService'
		),
		'ComplianceManagement',
		'Project'
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Internal Controls');
        $this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = array(
            'default' => array(
                'label' => __('General')
            ),
            'audits' => array(
                'label' => __('Audits')
            ),
            'maintenances' => array(
                'label' => __('Maintenances')
            ),
            'status' => array(
                'label' => __('Status')
            ),
        );

        $this->fieldData = array(
            'name' => array(
                'label' => __('Name'),
                'description' => __('Name for this internal control (Firewalls, CCTV, Etc).'),
                'editable' => true,
                'inlineEdit' => true,
            ),
            'objective' => array(
                'label' => __('Objective'),
                'description' => __('Give a brief description of what this internal control does.'),
                'editable' => true,
                'inlineEdit' => true,
                'validate' => [
                	'mandatory' => false
                ]
            ),
            'documentation_url' => array(
                'label' => __('Documentation URL'),
                'description' => __('Insert the url where the documentation for this internal control is located (Wiki Page, Etc).'),
                'editable' => true,
                'inlineEdit' => true,
                'validate' => [
                	'mandatory' => false
                ]
            ),
            'security_service_type_id' => array(
                'label' => __('Status'),
                'description' => __('Design: controls in design are not shown outside this module<br />
                	Production: controls in production are shown across all system modules'),
                'editable' => true,
            ),
            'service_classification_id' => array(
                'label' => __('Classification'),
                'editable' => false,
                'hidden' => true,
            ),
            'Classification' => array(
                'label' => __('Tags'),
                'description' => __('Apply a tag to the control such as "Expensive", "Compliance Key" this can later be used to group or filter similar controls.'),
                'editable' => true,
                'inlineEdit' => true,
                'type' => 'tags',
                'options' => [$this, 'getClassifications'],
                'validate' => [
                	'mandatory' => false
                ],
            ),
            'ServiceOwner' => $UserFields->getFieldDataEntityData($this, 'ServiceOwner', [
				'label' => __('Service Owner'), 
				'description' => __('You can use this field in any way it fits best your organisation, for example the person responsable or accountable of ensuring this control is correctly operated. Remember you can setup notifications that point to this role to remind them of their responsabilities.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'Collaborator' => $UserFields->getFieldDataEntityData($this, 'Collaborator', [
				'label' => __('Collaborator'), 
				'description' => __('You can use this field in any way it fits best your organisation, for example to indicate the people that operates this control in a daily bases.'),
				'validate' => [
                	'mandatory' => false
                ],
                'quickAdd' => true,
                'inlineEdit' => true,
			]),
        	'opex' => array(
            	'label' => __('Cost (OPEX)'),
            	'description' => __('Insert the amount of OPEX this control costs per year or if not applicable set the value to zero'),
                'editable' => true,
                'inlineEdit' => true,
        	),
        	'capex' => array(
            	'label' => __('Cost (CAPEX)'),
            	'description' => __('Same as above but for CAPEX.'),
                'editable' => true,
                'inlineEdit' => true,
        	),
        	'resource_utilization' => array(
            	'label' => __('Resource Utilization'),
            	'description' => __('Input the time (in hours, days or whatever unit you find useful) that takes your team to keep this control audited (tested) and updated. This is important to keep again your budgets well organised.'),
                'editable' => true,
                'inlineEdit' => true,
        	),
        	'ServiceContract' => array(
        		'label' => __('Support Contracts'),
        		'description' => __('Select any applicable Support Contracts for this internal control'),
                'editable' => true,
                'inlineEdit' => true,
                'validate' => [
                	'mandatory' => false
                ],
                'quickAdd' => true,
    		),
    		'SecurityPolicy' => array(
        		'label' => __('Security Policy Items'),
        		'description' => __('Select one or more security policies (Control Catalogue / Security Policies) that are related to this control. Not having security policies mapped to a controls is an indication that you might not be using eramba correctly.'),
                'editable' => true,
                'inlineEdit' => true,
                'validate' => [
                	'mandatory' => false
                ],
                'quickAdd' => true,
    		),
    		'audit_calendar_type' => array(
    			'type' => 'select',
    			'editable' => true,
    			'label' => __('Choose calendar type'),
    			'description' => __('Choose which type of calendar you want to use'),
    			'renderHelper' => ['SecurityServices', 'calendarTypeField'],
    			'options' => [$this, 'getAuditsCalendarTypes'],
    			'group' => 'audits'
    		),
    		'audit_calendar_recurrence_start_date' => array(
                'type' => 'date',
                'editable' => true,
                'label' => __('Start Date'),
                'description' => __('Select start date for recurrence'),
                'renderHelper' => ['SecurityServices', 'calendarRecurrenceStartDateField'],
                'group' => 'audits'
            ),
    		'audit_calendar_recurrence_frequency' => [
    			'type' => 'number',
    			'editable' => true,
    			'label' => __('Frequency'),
    			'description' => __('Taking as a start the date selected above, input the number of audits that will be created using the periodicity selected below'),
    			'renderHelper' => ['SecurityServices', 'calendarRecurrenceFrequencyField'],
    			'group' => 'audits'
    		],
    		'audit_calendar_recurrence_period' => [
    			'type' => 'select',
    			'editable' => true,
    			'label' => __('Period'),
    			'description' => __('Select a periodicity that will be applied to the start date and frequency selected above'),
    			'options' => [$this, 'getRecurrencePeriods'],
    			'renderHelper' => ['SecurityServices', 'calendarRecurrencePeriodField'],
    			'group' => 'audits'
    		],
			'SecurityServiceAuditDate' => array(
				'type' => 'text',
				'editable' => true,
				'usable' => false,
                // 'type' => 'tags',
    			'label' => __('Yearly Audit Calendar'),
    			'group' => 'audits',
    			'renderHelper' => ['SecurityServices', 'auditsField'],
    			'description' => __('Create as many audit dates for this internal control. Dates defined here will create audit records for this and future calendar years. If you dont want to test this control simply dont add any date.'),
    			'validate' => [
                	'mandatory' => false
                ],
			),
			'audit_metric_description' => array(
        		'label' => __('Audit Methodology'),
        		'description' => __('Describe what evidence and what analysis is required for testing this control. If you choose not to audit controls, simply set NA.'),
        		'group' => 'audits',
                'editable' => true,
                'inlineEdit' => true,
                'renderHelper' => ['SecurityServices', 'auditMetricDescriptionField']
    		),
    		'audit_success_criteria' => array(
        		'label' => __('Audit Success Criteria'),
        		'description' => __('Describe what is the expected result of the audit in order to call it a "Pass". If you choose not to audit controls, simply set NA.'),
        		'group' => 'audits',
                'editable' => true,
                'inlineEdit' => true,
                'renderHelper' => ['SecurityServices', 'auditSuccessCriteriaField']
    		),
    		'AuditOwner' => $UserFields->getFieldDataEntityData($this, 'AuditOwner', [
				'label' => __('Audit Owner'),
				'group' => 'audits',
				'description' => __('Select one or more accounts that will act as auditors for this control'),
				'quickAdd' => true,
				'inlineEdit' => true,
				'renderHelper' => ['SecurityServices', 'auditOwnerField']
			]),
			'AuditEvidenceOwner' => $UserFields->getFieldDataEntityData($this, 'AuditEvidenceOwner', [
				'label' => __('Audit Evidence Owner'),
				'group' => 'audits',
				'description' => __('Select one or more accounts that will be asked for testing evidence'),
				'quickAdd' => true,
				'inlineEdit' => true,
				'renderHelper' => ['SecurityServices', 'AuditEvidenceOwnerField']
			]),
			'maintenance_calendar_type' => array(
    			'type' => 'select',
    			'editable' => true,
    			'label' => __('Choose calendar type'),
    			'description' => __('Choose which type of calendar you want to use'),
    			'renderHelper' => ['SecurityServices', 'calendarTypeField'],
    			'options' => [$this, 'getMaintenancesCalendarTypes'],
    			'group' => 'maintenances'
    		),
    		'maintenance_calendar_recurrence_start_date' => array(
                'type' => 'date',
                'editable' => true,
                'label' => __('Start Date'),
                'description' => __('Select start date for recurrence'),
                'renderHelper' => ['SecurityServices', 'calendarRecurrenceStartDateField'],
                'group' => 'maintenances'
            ),
    		'maintenance_calendar_recurrence_frequency' => [
    			'type' => 'number',
    			'editable' => true,
    			'label' => __('Frequency'),
    			'description' => __('Taking as a start the date selected above, input the number of audits that will be created using the periodicity selected below'),
    			'renderHelper' => ['SecurityServices', 'calendarRecurrenceFrequencyField'],
    			'group' => 'maintenances'
    		],
    		'maintenance_calendar_recurrence_period' => [
    			'type' => 'select',
    			'editable' => true,
    			'label' => __('Period'),
    			'description' => __('Select a periodicity that will be applied to the start date and frequency selected above'),
    			'options' => [$this, 'getRecurrencePeriods'],
    			'renderHelper' => ['SecurityServices', 'calendarRecurrencePeriodField'],
    			'group' => 'maintenances'
    		],
    		'SecurityServiceMaintenanceDate' => array(
				'type' => 'text',
				'editable' => true,
				'usable' => false,
                // 'type' => 'tags',
    			'label' => __('Security Service Maintenance Date'),
    			'group' => 'maintenances',
    			'renderHelper' => ['SecurityServices', 'auditsField'],
    			'description' => __('Select the months in the year where this maintenance must take place. If you choose not to audit controls, simply dont create any date.'),
    			'validate' => [
                	'mandatory' => false
                ],
			),
    		'maintenance_metric_description' => array(
        		'label' => __('Maintenance Task'),
        		'description' => __('Describe what is the maintenance task for this control'),
        		'group' => 'maintenances',
                'editable' => true,
                'inlineEdit' => true,
                'renderHelper' => ['SecurityServices', 'maintenanceMetricDescriptionField']
    		),
    		'MaintenanceOwner' => $UserFields->getFieldDataEntityData($this, 'MaintenanceOwner', [
				'label' => __('Maintenance Owner'),
				'group' => 'maintenances',
				'description' => __('Select one or more accounts that must carry on the task defined for this maintenance'),
				'quickAdd' => true,
				'inlineEdit' => true,
				'renderHelper' => ['SecurityServices', 'maintenanceOwnerField']
			]),
    		'audits_all_done' => array(
    			'label' => __('Audits all done'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'audits_last_missing' => array(
    			'label' => __('Last audit missing'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'audits_last_passed' => array(
    			'label' => __('Last audit failed'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'audits_improvements' => array(
    			'label' => __('Audits Improvements'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'audits_status' => array(
    			'label' => __('Audits status'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'maintenances_all_done' => array(
				'label' => __('Maintances all done'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'maintenances_last_missing' => array(
    			'label' => __('Last maintance missing'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'maintenances_last_passed' => array(
    			'label' => __('Last maintance failed'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'ongoing_security_incident' => array(
    			'label' => __('Ongoing Security Incident'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'control_with_issues' => array(
    			'label' => __('Control with Issues'),
    			'group' => 'status',
				'type' => 'toggle',
				'hidden' => true,
			),
			'SecurityServiceAudit' => array(
    			'label' => __('Security Service Audit'),
    			'editable' => false,
			),
			'SecurityServiceMaintenance' => array(
    			'label' => __('Security Service Maintenance'),
				'editable' => false,
			),
			// 'SecurityServiceMaintenanceDate' => array(
   //  			'label' => __('Security Service Maintenance Date'),
			// 	'hidden' => true,
			// ),
			'Issue' => array(
    			'label' => __('Issues'),
				'hidden' => true,
			),
			'Risk' => array(
    			'label' => __('Asset Risks'),
				'hidden' => true
			),
			'ThirdPartyRisk' => array(
    			'label' => __('Third Party Risks'),
				'hidden' => true
			),
			'BusinessContinuity' => array(
    			'label' => __('Business Continuities'),
				'hidden' => true
			),
			'SecurityIncident' => array(
    			'label' => __('Security Incidents'),
				'hidden' => true,
			),
			'DataAsset' => array(
    			'label' => __('Data Asset Flows'),
				'hidden' => true
			),
			'ComplianceManagement' => array(
    			'label' => __('Compliance Managements'),
				'hidden' => true,
			),
			'Project' => array(
				'label' => __('Projects'),
				'description' => __('Select from the drop down one or more projects (Security Operations / Project Management) asociated with this internal control'),
				'editable' => true,
				'options' => array($this, 'getProjects'),
				'validate' => [
                	'mandatory' => false
                ],
                'quickAdd' => true,
			)
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
				'SECSERV_OWNER' => $UserFields->getNotificationSystemData('ServiceOwner', [
					'name' => __('Security Service Owner')
				]),
				'SECSERV_COLLABORATOR' => $UserFields->getNotificationSystemData('Collaborator', [
					'name' => __('Security Service Collaborators')
				]),
				'SECSERV_AUDITMETRIC' => array(
					'field' => 'SecurityService.audit_metric_description',
					'name' => __('Security Service Metric Description')
				),
				'SECSERV_AUDITCRITERIA' => array(
					'field' => 'SecurityService.audit_success_criteria',
					'name' => __('Security Service Success Criteria')
				),
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Security Services'),
			'pdf_file_name' => __('security_services'),
			'csv_file_name' => __('security_services'),
			'additional_actions' => array(
				'SecurityServiceAudit' => __('Audits'),
				'SecurityServiceMaintenance' => __('Maintenances'),
			),
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
            'view_item' => AppIndexCrudAction::VIEW_ITEM_QUERY,
            'use_new_filters' => true,
            'add' => true
		);

		parent::__construct($id, $table, $ds);
	}

	const CALENDAR_TYPE_NO_DATES = 0;
	const CALENDAR_TYPE_SPECIFIC_DATES = 1;
	const CALENDAR_TYPE_RECURRENCE_DATE = 2;
	public static function getCalendarTypes($type = null, $which = null)
	{
		$title = __('No Dates Required');
		if ($which === 'audits') {
			$title = __('No Audits Required');
		} elseif ($which === 'maintenances') {
			$title = __('No Maintenances Required');
		}

		$types = [
			self::CALENDAR_TYPE_NO_DATES => $title,
			self::CALENDAR_TYPE_SPECIFIC_DATES => __('Select specific audit dates'),
			self::CALENDAR_TYPE_RECURRENCE_DATE => __('Select recurrence date')
		];

		if ($type !== null && array_key_exists($type, $types)) {
			return $types[$type];
		} else {
			return $types;
		}
	}

	public function getAuditsCalendarTypes()
	{
		return self::getCalendarTypes(null, 'audits');
	}

	public function getMaintenancesCalendarTypes()
	{
		return self::getCalendarTypes(null, 'maintenances');
	}

	const CALENDAR_PERIOD_DAY = 1;
	const CALENDAR_PERIOD_WEEK = 2;
	const CALENDAR_PERIOD_MONTH = 3;
	const CALENDAR_PERIOD_QUARTER = 4;
	const CALENDAR_PERIOD_SEMESTER = 5;
	public static function getRecurrencePeriods($period = null)
	{
		$periods = [
			self::CALENDAR_PERIOD_DAY => __('Day'),
			self::CALENDAR_PERIOD_WEEK => __('Week'),
			self::CALENDAR_PERIOD_MONTH => __('Monthly'),
			self::CALENDAR_PERIOD_QUARTER => __('Quarter'),
			self::CALENDAR_PERIOD_SEMESTER => __('Semester')
		];

		if ($periods !== null && array_key_exists($period, $periods)) {
			return $periods[$period];
		} else {
			return $periods;
		}
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
				->textField('objective', [
					'showDefault' => true
				])
				->textField('documentation_url')
				->multipleSelectField('security_service_type_id', [ClassRegistry::init('SecurityServiceType'), 'getList'])
				->userField('ServiceOwner', 'ServiceOwner', [
					'showDefault' => true
				])
				->userField('Collaborator', 'Collaborator', [
					'showDefault' => true
				])
				->multipleSelectField('Classification-name', [$this, 'getClassifications'], [
					'label' => __('Tags')
				])
				->numberField('opex', [
					'label' => __('Opex'),
					'showDefault' => true
				])
				->numberField('capex', [
					'label' => __('Capex'),
					'showDefault' => true
				])
				->numberField('resource_utilization', [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_control_with_issues', 'control_with_issues')
			->group('SecurityServiceAudit', [
				'name' => __('Audit')
			])
				->dateField('SecurityServiceAudit-planned_date', [
					'label' => __('Audit Date'),
				])
				->textField('audit_metric_description')
				->textField('audit_success_criteria')
				->userField('AuditOwner', 'AuditOwner')
				->userField('AuditEvidenceOwner', 'AuditEvidenceOwner')
				->objectStatusField('ObjectStatus_audits_last_not_passed', 'audits_last_not_passed')
				->objectStatusField('ObjectStatus_audits_last_missing', 'audits_last_missing')
			->group('SecurityServiceMaintenance', [
				'name' => __('Maintenance')
			])
				->dateField('SecurityServiceMaintenance-planned_date', [
					'label' => __('Maintance Date'),
				])
				->textField('maintenance_metric_description')
				->userField('MaintenanceOwner', 'MaintenanceOwner')
				->objectStatusField('ObjectStatus_maintenances_last_not_passed', 'maintenances_last_not_passed')
				->objectStatusField('ObjectStatus_maintenances_last_missing', 'maintenances_last_missing');

		$this->SecurityIncident->relatedFilters($advancedFilterConfig);
		$this->ComplianceManagement->relatedFilters($advancedFilterConfig);
		$this->Risk->relatedFilters($advancedFilterConfig);
		$this->ThirdPartyRisk->relatedFilters($advancedFilterConfig);
		$this->BusinessContinuity->relatedFilters($advancedFilterConfig);

		$advancedFilterConfig
			->group('DataAsset', [
				'name' => __('Data Flow Analysis')
			])
				->multipleSelectField('DataAssetInstance-asset_id', [ClassRegistry::init('Asset'), 'getList'], [
					'label' => __('Asset'),
					'findField' => 'DataAsset.DataAssetInstance.asset_id',
					'fieldData' => 'DataAsset.DataAssetInstance.asset_id'
				])
				->multipleSelectField('DataAsset', [ClassRegistry::init('Risk'), 'getList'], [
					'label' => __('Data Asset Flow')
				])
				->multipleSelectField('DataAsset-data_asset_status_id', [ClassRegistry::init('DataAsset'), 'statuses'], [
					'label' => __('Data Asset Flow Type')
				]);

		$this->SecurityPolicy->relatedFilters($advancedFilterConfig);
		$this->Project->relatedFilters($advancedFilterConfig);
		$this->ServiceContract->relatedFilters($advancedFilterConfig);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
			->group('SecurityService', [
				'name' => __('Internal Control')
			])
				->multipleSelectField('SecurityService', [$this, 'getList'], [
                    'label' => __('Internal Control')
                ])
                ->textField('SecurityService-objective', [
                    'label' => __('Internal Control Description')
                ]);

		return $advancedFilterConfig;
	}

	public function getImportToolConfig()
	{
		return [
			'SecurityService.name' => [
				'name' => __('Name'),
				'headerTooltip' => __('This field is mandatory')
			],
			'SecurityService.objective' => [
				'name' => __('Objective'),
				'headerTooltip' => __('This field is mandatory')
			],
			'SecurityService.documentation_url' => [
				'name' => __('Documentation URL'),
				'headerTooltip' => __('Optional, you can leave this field blank')
			],
			'SecurityService.security_service_type_id' => [
				'name' => __('Status'),
				'model' => 'SecurityServiceType',
				'headerTooltip' => __(
					'Mandatory, can be one of the following numbers: %s',
					ImportToolModule::formatList(
						$this->SecurityServiceType->find('list', ['recursive' => -1])
					)
				)
			],
			'SecurityService.Project' => [
				'name' => __('Projects'),
				'model' => 'Project',
				'headerTooltip' => __('Optional and accepts multiple names separated by "|". You need to enter the name of a project, you can find them at Security Operations / Project Management'),
				'objectAutoFind' => true,
			],
			'SecurityService.Classification' => [
				'name' => __('Tags'),
				'model' => 'Classification',
				'callback' => [
					'beforeImport' => [$this, 'convertClassificationsImport']
				],
				'headerTooltip' => __('Optional and accepts multiple values separated by "|". For example "Critical|SOX|PCI"')
			],
			'SecurityService.ServiceOwner' => UserFields::getImportArgsFieldData('ServiceOwner', [
				'name' => __('Service Owner')
			], true),
			'SecurityService.Collaborator' => UserFields::getImportArgsFieldData('Collaborator', [
				'name' => __('Collaborator')
			], true),
			'SecurityService.opex' => [
				'name' => __('OPEX'),
				'headerTooltip' => __('Mandatory, it requires a numerical value')
			],
			'SecurityService.capex' => [
				'name' => __('CAPEX'),
				'headerTooltip' => __('Mandatory, it requires a numerical value')
			],
			'SecurityService.resource_utilization' => [
				'name' => __('Resource Utilization'),
				'headerTooltip' => __('Mandatory, it requires a numerical value')
			],
			'SecurityService.SecurityPolicy' => [
				'name' => __('Security Policies'),
				'model' => 'SecurityPolicy',
				'headerTooltip' => __('Optional and accepts multiple names separated by "|". You can get the name of a policy from Control Catalogue / Policy Management'),
				'objectAutoFind' => true,
			],
			'SecurityService.AuditOwner' => UserFields::getImportArgsFieldData('AuditOwner', [
				'name' => __('Audit Owner'),
				'headerTooltip' => __('This role is typically used to record the individual that lead the audit (testing) process.'),
			]),
			'SecurityService.AuditEvidenceOwner' => UserFields::getImportArgsFieldData('AuditEvidenceOwner', [
				'name' => __('Audit Evidence Owner'),
				'headerToolTip' => __('This role is typically used to record and notify the individual that must provide evidence. Remember you can send regular notifications requesting evidence to this role.')
			]),
			'SecurityService.audit_metric_description' => [
				'name' => __('Audit Metric'),
				'headerTooltip' => __('Mandatory, you need to insert some text or NA if you are not interested in this feature')
			],
			'SecurityService.audit_success_criteria' => [
				'name' => __('Audit Criteria'),
				'headerTooltip' => __('Mandatory, you need to insert some text or NA if you are not interested in this feature')
			],
			'SecurityServiceAuditDate' => [
				'name' => __('Audit Date'),
				'model' => 'SecurityServiceAuditDate',
				'callback' => [
					'beforeImport' => [$this, 'convertAuditDateImport'],
					'beforeExport' => [$this, 'convertAuditDateExport']
				],
				'headerTooltip' => __('Optional, you can insert one date with the format DD-MM. Bare in mind the delimiter is a "-"')
			],
			'SecurityService.MaintenanceOwner' => UserFields::getImportArgsFieldData('MaintenanceOwner', [
				'name' => __('Maintenance Owner'),
				'headerToolTip' => __('Who executed the task?')
			]),
			'SecurityService.maintenance_metric_description' => [
				'name' => __('Maintenance Task'),
				'headerTooltip' => __('Mandatory - you can set NA if you wont want to use this feature')
			],
			'SecurityServiceMaintenanceDate' => [
				'name' => __('Maintenance Date'),
				'model' => 'SecurityServiceMaintenanceDate',
				'callback' => [
					'beforeImport' => [$this, 'convertAuditDateImport'],
					'beforeExport' => [$this, 'convertMaintenanceDateExport']
				],
				'headerHint' => 'Day-Month',
				'headerTooltip' => __('Optional, you can insert one date with the format DD-MM. Bare in mind the delimiter is a "-"')
			],
		];
	}
	
	public function getNotificationSystemConfig()
	{
		return parent::getNotificationSystemConfig();
	}

	public function getObjectStatusConfig() {
        return [
        	'audits_all_done' => [// delete
                'title' => __('Audits all done'),
                'callback' => [$this, 'statusAuditsAllDone'],
                'hidden' => true,
            ],
            'audits_not_all_done' => [// delete
                'title' => __('Audits not all done'),
                'callback' => [$this, 'statusAuditsNotAllDone'],
                'hidden' => true,
            ],
            'audits_last_passed' => [// delete
                'title' => __('Last audit not failed'),
                'callback' => [$this, 'statusAuditsLastPassed'],
                'type' => 'danger',
                'hidden' => true,
                'regularTrigger' => true,
            ],
            'audits_last_not_passed' => [
                'title' => __('Last Audit Failed'),
                'callback' => [$this, 'statusAuditsLastNotPassed'],
                'type' => 'danger',
                'trigger' => [
                	$this->Risk,
                    $this->ThirdPartyRisk,
                    $this->BusinessContinuity,
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.security_service_audits_last_not_passed'
                    ],
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.controls_with_failed_audits'
                    ],
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'audits_last_missing' => [
                'title' => __('Last Audit Expired'),
                'callback' => [$this, 'statusAuditsLastMissing'],
                'trigger' => [
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.controls_with_missing_audits'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.security_service_audits_last_missing'
                    ],
                    $this->Risk,
                    $this->ThirdPartyRisk,
                    $this->BusinessContinuity,
                ],
                'regularTrigger' => true,
            ],
            'audits_improvements' => [// delete
            	'title' => __('Being fixed'),
                'callback' => [$this, 'statusAuditsImprovements'],
                'hidden' => true,
                'regularTrigger' => true,
            ],
            'maintenances_all_done' => [// delete
            	'title' => __('Maintenances all done'),
                'callback' => [$this, 'statusMaintenancesAllDone'],
                'hidden' => true,
            ],
            'maintenances_not_all_done' => [//issue // delete
            	'title' => __('Maintenances not all done'),
                'callback' => [$this, 'statusMaintenancesNotAllDone'],
                'hidden' => true,
            ],
            'maintenances_last_passed' => [// delete
            	'title' => __('Last maintenance not failed'),
                'callback' => [$this, 'statusMaintenancesLastPassed'],
                'type' => 'danger',
                'hidden' => true,
                'regularTrigger' => true,
            ],
            'maintenances_last_not_passed' => [
            	'title' => __('Last Maintenance Failed'),
                'callback' => [$this, 'statusMaintenancesLastNotPassed'],
                'trigger' => [
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.security_service_maintenances_last_not_passed'
                    ],
                ],
                'type' => 'danger',
                'regularTrigger' => true,
            ],
            'maintenances_last_missing' => [//issue
                'title' => __('Last Maintenance Expired'),
                'callback' => [$this, 'statusMaintenancesLastMissing'],
                'trigger' => [
                    $this->Risk,
                    $this->ThirdPartyRisk,
                    $this->BusinessContinuity,
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.security_service_maintenances_last_missing'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'control_with_issues' => [//
                'title' => __('Control Issues'),
                'callback' => [$this, 'statusControlWithIssues'],
                'type' => 'danger',
                'trigger' => [
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.controls_with_issues'
                    ],
                    $this->Risk,
                    $this->ThirdPartyRisk,
                    $this->BusinessContinuity,
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.control_with_issues'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.control_with_issues'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.control_with_issues'
                    ],
                    [
                    	'model' => $this->ComplianceManagement,
                    	'trigger' => 'ObjectStatus.trigger.security_service_control_with_issues'
                    ],
                ]
            ],
            'control_in_design' => [//
                'title' => __('Control in Design'),
                'callback' => [$this, 'statusControlInDesign'],
                'storageSelf' => false,
                'trigger' => [
                    $this->Risk,
                    $this->ThirdPartyRisk,
                    $this->BusinessContinuity,
                    [
                    	'model' => $this->ComplianceManagement,
                    	'trigger' => 'ObjectStatus.trigger.security_service_control_in_design'
                    ],
                ]
            ],
            'ongoing_incident' => [//
            	'title' => __('Incident Ongoing'),
                'inherited' => [
                	'SecurityIncident' => 'ongoing_incident'
            	],
            	'storageSelf' => false
            ],
            'project_expired' => [
            	'title' => __('Project Expired'),
                'inherited' => [
                	'Project' => 'expired'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'project_expired_tasks' => [
            	'title' => __('Project Task Expired'),
                'inherited' => [
                	'Project' => 'expired_tasks'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'project_ongoing' => [
            	'title' => __('Project Ongoing'),
                'inherited' => [
                	'Project' => 'ongoing'
            	],
            	'type' => 'success',
                'storageSelf' => false
            ],
            'project_planned' => [
            	'title' => __('Project Planned'),
                'inherited' => [
                	'Project' => 'planned'
            	],
            	'type' => 'success',
                'storageSelf' => false
            ],
            'project_closed' => [
            	'title' => __('Project Closed'),
                'inherited' => [
                	'Project' => 'closed'
            	],
            	'type' => 'success',
                'storageSelf' => false
            ],
            'project_no_updates' => [
            	'title' => __('Project Missing Updates'),
                'inherited' => [
                	'Project' => 'no_updates'
            	],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
        ];
    }

    public function getMacrosConfig()
	{
		return [
			'prefix' => 'internal_control',
			'seed' => [
				[$this, 'customMacros']
			]
		];
	}

	public function customMacros($Collection)
	{
		$groupSettings = $this->getMacroGroupModelSettings();

		$Macro = new Macro($this->getMacroAlias('compliance_items_list'), __('List of Related Compliance Items'), null, ['SecurityServicesHelper', 'complianceItemsList']);
		$Collection->add($Macro);

		$Macro = new Macro($this->getMacroAlias('risk_items_list'), __('List of Related Risk Items'), null, ['SecurityServicesHelper', 'riskItemsList']);
		$Collection->add($Macro);

		$Macro = new Macro($this->getMacroAlias('data_flow_items_list'), __('List of Related Data Flow Items'), null, ['SecurityServicesHelper', 'dataAssetItemsList']);
		$Collection->add($Macro);
	}

    public function getSectionInfoConfig()
    {
        return [
            'map' => [
                'Risk',
                'ThirdPartyRisk',
                'BusinessContinuity',
                'SecurityServiceAudit',
                'SecurityServiceIssue',
                'SecurityServiceMaintenance',
                'Project' => [
                    'ProjectAchievement',
                ],
                'SecurityPolicy',
                'ComplianceManagement',
            ]
        ];
    }

    public function getReportsConfig()
    {
    	$auditResults = [
			'title' => __('Audits by Result'),
			'description' => __('This chart shows the count of pass, failed and missing audits.'),
			'type' => ReportBlockChartSetting::TYPE_PIE,
			'className' => 'AuditResultsChart',
			'params' => [
			]
		];

		$auditResultsCurrentYear = [
			'title' => __('Audits by Result (current calendar year)'),
			'description' => __('This chart shows the proportion of pass, failed and missing audits for this current year.'),
			'type' => ReportBlockChartSetting::TYPE_PIE,
			'className' => 'AuditResultsChart',
			'params' => [
				'percentage' => true,
				'year' => date('Y')
			]
		];

		$auditResultsPastYear = [
			'title' => __('Audits by Result (past calendar year)'),
			'description' => __('This chart shows the proportion of pass, failed and missing audits for past year.'),
			'type' => ReportBlockChartSetting::TYPE_PIE,
			'templateType' => ReportTemplate::TYPE_SECTION,
			'className' => 'AuditResultsChart',
			'params' => [
				'percentage' => true,
				'year' => date('Y', strtotime('-1 year'))
			]
		];

		$controlsByMitigation = [
			'title' => __('Controls by Mitigation'),
			'description' => __('This ven diagram shows the proportion on how controls are used against Asset Risks, Third Party Risks, Business Risks, Compliance and Data Flow Analysis.'),
			'type' => ReportBlockChartSetting::TYPE_RADAR,
			'dataFn' => 'associationsChart',
		];

		$auditResultsOverTime = [
			'title' => __('Audits Results Over Time'),
			'description' => __('This chart shows all audit records over time which ones failed, pass, are missing or are scheduled in the future. It also shows the quantity based on the size of the circle.'),
			'type' => ReportBlockChartSetting::TYPE_PUNCH_CARD,
			'dataFn' => 'auditResultTimelineChart'
		];

		return [
			'finder' => [
				'options' => [
					'contain' => [
						'SecurityServiceType',
						'ServiceClassification',
						'Classification',
						'SystemRecord',
						'Issue',
						'SecurityServiceAudit' => [
							'SecurityService',
							'SecurityServiceAuditImprovement' => [
								'Project'
							],
							'CustomFieldValue',
							'AuditOwner',
							'AuditOwnerGroup',
							'AuditEvidenceOwner',
							'AuditEvidenceOwnerGroup'
						],
						'SecurityServiceAuditDate',
						'SecurityServiceMaintenance' => [
							'SecurityService',
							'CustomFieldValue',
							'MaintenanceOwner',
							'MaintenanceOwnerGroup'
						],
						'SecurityServiceMaintenanceDate',
						'CustomFieldValue',
						'SecurityIncident',
						'DataAsset' => [
							'DataAssetInstance' => [
								'Asset'
							]
						],
						'ServiceContract',
						'SecurityPolicy' => [
							'SecurityPolicyDocumentType'
						],
						'Risk',
						'ThirdPartyRisk',
						'BusinessContinuity',
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
						'MaintenanceOwnerGroup',
						'ComplianceManagement' => [
							'CompliancePackageItem' => [
								'CompliancePackage' => [
									'CompliancePackageRegulator'
								]
							]
						],
					]
				]
			],
			'table' => [
				'model' => [
					'SecurityServiceAudit', 'SecurityServiceMaintenance',
				]
			],
			'chart' => [
				1 => array_merge($auditResults, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				2 => array_merge($auditResults, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
				3 => array_merge($controlsByMitigation, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				4 => array_merge($controlsByMitigation, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
				5 => array_merge($auditResultsOverTime, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				6 => array_merge($auditResultsOverTime, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
				7 => [
					'title' => __('Related Compliance Items'),
					'description' => __('This tree chart shows all related compliance requirements linked to this item.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'relatedComplianceItemsChart',
				],
				8 => [
					'title' => __('Related Risk Items'),
					'description' => __('This tree chart shows all related risk items linked.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'relatedRiskItemsChart',
				],
				9 => [
					'title' => __('Related Policy Items'),
					'description' => __('This tree chart shows all related policies linked to this item.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'relatedPolicyItemsChart',
				],
				10 => [
					'title' => __('Top 10 Fail Controls by Testing (by proportion)'),
					'description' => __('This chart shows the top ten controls for the last calendar year that failed the largest proportion of audits.'),
					'type' => ReportBlockChartSetting::TYPE_BAR,
					'templateType' => ReportTemplate::TYPE_SECTION,
					'className' => 'FailedAuditsChart',
					'params' => [
						'percentage' => true,
						'year' => date('Y')
					]
				],
				11 => [
					'title' => __('Top 10 Fail Controls by Testing (by counter)'),
					'description' => __('This chart shows the top ten controls for the last calendar year based on the total number of failed audits. A second bar shows the total number of audits for the last calendar year.'),
					'type' => ReportBlockChartSetting::TYPE_BAR,
					'templateType' => ReportTemplate::TYPE_SECTION,
					'className' => 'FailedAuditsChart',
					'params' => [
						'year' => date('Y')
					]
				],
				12 => array_merge($auditResultsCurrentYear, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				13 => array_merge($auditResultsCurrentYear, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
				14 => array_merge($auditResultsPastYear, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				15 => array_merge($auditResultsPastYear, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
			]
		];
	}

    public function statusAuditsAllDone() {
    	$data = $this->SecurityServiceAudit->find('count', [
			'conditions' => [
				'SecurityServiceAudit.security_service_id' => $this->id,
				'SecurityServiceAudit.result IS NULL',
				'SecurityServiceAudit.planned_date < DATE(NOW())'
			],
			'recursive' => -1
		]);

    	return empty($data);
    }

    public function statusAuditsNotAllDone() {
    	return !$this->statusAuditsAllDone();
    }

    public function statusAuditsLastPassed() {
		$data = $this->SecurityServiceAudit->find('first', [
			'conditions' => [
				'SecurityServiceAudit.security_service_id' => $this->id,
				'SecurityServiceAudit.planned_date < DATE(NOW())',
			],
			'fields' => [
				'SecurityServiceAudit.result'
			],
			'order' => [
				'SecurityServiceAudit.planned_date' => 'DESC'
			],
			'recursive' => -1
		]);

		if (empty($data) || in_array($data['SecurityServiceAudit']['result'], [AUDIT_PASSED, null])) {
			return true;
		}

    	return false;
    }

    public function statusAuditsLastNotPassed() {
    	return !$this->statusAuditsLastPassed();
    }

    public function statusAuditsLastMissing() {
		$data = $this->SecurityServiceAudit->find('first', [
			'conditions' => [
				'SecurityServiceAudit.security_service_id' => $this->id,
				'SecurityServiceAudit.planned_date < DATE(NOW())',
			],
			'fields' => [
				'SecurityServiceAudit.result'
			],
			'order' => [
				'SecurityServiceAudit.planned_date' => 'DESC'
			],
			'recursive' => -1
		]);

    	return (!empty($data) && $data['SecurityServiceAudit']['result'] === null);
    }

    public function statusAuditsImprovements() {
    	$data = $this->ProjectsSecurityService->find('count', [
			'conditions' => [
				'ProjectsSecurityService.security_service_id' => $this->id
			],
			'recursive' => -1
		]);

		if (!empty($data)) {
			return true;
		}

		$data = $this->SecurityServiceAudit->find('count', [
			'conditions' => [
				'SecurityServiceAudit.security_service_id' => $this->id,
				'SecurityServiceAudit.planned_date < DATE(NOW())',
				'SecurityServiceAudit.result IS NOT NULL'
			],
			'fields' => '*',
			'order' => [
				'SecurityServiceAudit.planned_date' => 'DESC'
			],
			'joins' => [
                [
                    'table' => 'security_service_audit_improvements',
                    'alias' => 'SecurityServiceAuditImprovement',
                    'type' => 'INNER',
                    'conditions' => [
                        'SecurityServiceAuditImprovement.security_service_audit_id = SecurityServiceAudit.id',
                    ]
                ],
            ],
			'recursive' => -1
		]);

		if (!empty($data)) {
			return true;
		}

    	return false;
    }

    public function statusMaintenancesAllDone() {
    	$data = $this->SecurityServiceMaintenance->find('count', [
			'conditions' => [
				'SecurityServiceMaintenance.security_service_id' => $this->id,
				'SecurityServiceMaintenance.result IS NULL',
				'SecurityServiceMaintenance.planned_date < DATE(NOW())'
			],
			'recursive' => -1
		]);

    	return empty($data);
    }

    public function statusMaintenancesNotAllDone() {
    	return !$this->statusMaintenancesAllDone();
    }

    public function statusMaintenancesLastPassed() {
    	$data = $this->SecurityServiceMaintenance->find('first', [
			'conditions' => [
				'SecurityServiceMaintenance.security_service_id' => $this->id,
				'SecurityServiceMaintenance.planned_date < DATE(NOW())',
			],
			'fields' => [
				'SecurityServiceMaintenance.result'
			],
			'order' => [
				'SecurityServiceMaintenance.planned_date' => 'DESC'
			],
			'recursive' => -1
		]);

		if (empty($data) || in_array($data['SecurityServiceMaintenance']['result'], [AUDIT_PASSED, null])) {
			return true;
		}

    	return false;
    }

    public function statusMaintenancesLastNotPassed() {
    	return !$this->statusMaintenancesLastPassed();
    }

    public function statusMaintenancesLastMissing() {
    	$data = $this->SecurityServiceMaintenance->find('first', [
			'conditions' => [
				'SecurityServiceMaintenance.security_service_id' => $this->id,
				'SecurityServiceMaintenance.planned_date < DATE(NOW())',
			],
			'fields' => [
				'SecurityServiceMaintenance.result'
			],
			'order' => [
				'SecurityServiceMaintenance.planned_date' => 'DESC'
			],
			'recursive' => -1
		]);

    	return (!empty($data) && $data['SecurityServiceMaintenance']['result'] === null);
    }

    public function _statusOngoingCorrectiveActions() {
		// check projects by status
		$data = $this->Project->find('all', [
			'conditions' => [
				'ProjectsSecurityService.security_service_id' => $this->id,
				'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED
			],
			'joins' => [
                [
                    'table' => 'projects_security_services',
                    'alias' => 'ProjectsSecurityService',
                    'type' => 'INNER',
                    'conditions' => [
                        'ProjectsSecurityService.project_id = Project.id'
                    ]
                ],
            ],
			'recursive' => -1
		]);

		if (!empty($data)) {
			return true;
		}

		// or also check projects associated to audit improvements by status
		$Improvement = $this->SecurityServiceAudit->SecurityServiceAuditImprovement;
		$data = $Improvement->ProjectsSecurityServiceAuditImprovement->find('count', [
			'conditions' => [
				'SecurityServiceAudit.security_service_id' => $this->id,
				'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED
			],
			'joins' => $Improvement->getRelatedJoins(),
			'recursive' => -1
		]);

		if (!empty($data)) {
			return true;
		}

    	return false;
    }

    public function statusControlWithIssues() {
    	$data = $this->Issue->find('count', [
			'conditions' => [
				'Issue.model' => 'SecurityService',
				'Issue.foreign_key' => $this->id,
				'Issue.status' => ISSUE_OPEN
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function statusControlInDesign() {
    	$data = $this->find('count', [
			'conditions' => [
				'SecurityService.id' => $this->id,
				'SecurityService.security_service_type_id' => SECURITY_SERVICE_DESIGN,
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

	public function beforeValidate($options = array()) {
		$ret = true;
		
		// for import tool, we need to specify default value for calendar types before validation
		if (isset($options['import']) && $options['import'] === true) {
			$this->data['SecurityService']['audit_calendar_type'] = self::CALENDAR_TYPE_SPECIFIC_DATES;
			$this->data['SecurityService']['maintenance_calendar_type'] = self::CALENDAR_TYPE_SPECIFIC_DATES;
		}

		//
		// Remove validation when no audits or maintenances are created
		if (isset($this->data['SecurityService']['audit_calendar_type']) && $this->data['SecurityService']['audit_calendar_type'] == self::CALENDAR_TYPE_NO_DATES) {
			$this->validator()->remove('audit_metric_description');
			$this->validator()->remove('audit_success_criteria');
			$this->validator()->remove('AuditOwner');
			$this->validator()->remove('AuditEvidenceOwner');
		}

		if (isset($this->data['SecurityService']['maintenance_calendar_type']) && $this->data['SecurityService']['maintenance_calendar_type'] == self::CALENDAR_TYPE_NO_DATES) {
			$this->validator()->remove('maintenance_metric_description');
			$this->validator()->remove('MaintenanceOwner');
		}
		// 

		// audits and mantenances date validation
		$dateFields = array('SecurityServiceAuditDate', 'SecurityServiceMaintenanceDate');
		foreach ($dateFields as $field) {
			if (!empty($this->data[$field])) {
				foreach ($this->data[$field] as $key => $date) {
					$formattedDate = sprintf('%s-%s-%s', $date['day'], $date['month'], date('Y'));
					$ret &= $valid = Validation::date($formattedDate, array('dmy'));

					if (empty($valid)) {
						$this->invalidate($field . '_' . $key, __('This date is not valid.'));
						$this->invalidate($field, __('One or more dates are not valid.'));
					}
				}
			}
		}
		
		if (isset($this->data['SecurityService']['SecurityPolicy'])) {
			$this->invalidateRelatedNotExist('SecurityPolicy', 'SecurityPolicy', $this->data['SecurityService']['SecurityPolicy']);
		}

		if (isset($this->data['SecurityService']['Project'])) {
			$this->invalidateRelatedNotExist('Project', 'Project', $this->data['SecurityService']['Project']);
		}

		if (isset($this->data['SecurityService']['security_service_type_id'])) {
			$this->invalidateRelatedNotExist('SecurityServiceType', 'security_service_type_id', $this->data['SecurityService']['security_service_type_id']);
		}

		return $ret;
	}

	public function beforeSave($options = array()) {
		if (isset($this->data['SecurityService']['security_service_type_id'])
			&& $this->data['SecurityService']['security_service_type_id'] == SECURITY_SERVICE_DESIGN
		) {
			$this->setEmptyPoductionJoins();
		}
				
		// transforms the data array to save the HABTM relation
    	// $this->transformDataToHabtm(array('ServiceContract', 'SecurityPolicy', 'Project', 'SecurityIncident', 'DataAsset', 'ComplianceManagement'
		// ));

		//$this->disableDesignValidation();
		$this->updateAuditsAndMaintenances();

		return true;
	}

	public function afterSave($created, $options = array()) {
		$ret = true;
		if (!empty($this->id)) {
			// $this->Risk->pushStatusRecords();
			// $ret = $this->Risk->saveCustomStatuses($this->getSecurityServiceRisks($this->id));
			// $this->Risk->holdStatusRecords();

			// $this->ThirdPartyRisk->pushStatusRecords();
			// $ret &= $this->ThirdPartyRisk->saveCustomStatuses($this->getSecurityServiceTpRisks($this->id));
			// $this->ThirdPartyRisk->holdStatusRecords();

			// $this->BusinessContinuity->pushStatusRecords();
			// $ret &= $this->BusinessContinuity->saveCustomStatuses($this->getSecurityServiceBusinessRisks($this->id));
			// $this->BusinessContinuity->holdStatusRecords();
			
			if (isset($this->data['SecurityService']['Classification'])) {
				$this->Classification->deleteAll(array(
					'Classification.security_service_id' => $this->id
				));

				if (!empty($this->data['SecurityService']['Classification'])) {
					$this->saveClassifications($this->data['SecurityService']['Classification'], $this->id);
				}
			}

			if (isset($this->data['SecurityServiceAuditDate'])) {
				// $this->saveAuditDates($this->data['SecurityServiceAuditDate'], $this->id);
				// $this->SecurityServiceAuditDate->deleteAll(array(
					// 'SecurityServiceAuditDate.security_service_id' => $this->id
				// ));
				
				// if (!empty($this->data['SecurityServiceAuditDate'])) {
				// 	$this->saveAuditsJoins($this->data['SecurityServiceAuditDate'], $this->id);
				// }
			}
			
			if (isset( $this->data['SecurityServiceMaintenanceDate'])) {
				// 	$this->saveMaintenanceDates($this->data['SecurityService']['maintenance_calendar'], $this->id);
				// $this->SecurityServiceMaintenanceDate->deleteAll(array(
				// 	'SecurityServiceMaintenanceDate.security_service_id' => $this->id
				// ));
				// if (!empty($this->data['SecurityServiceMaintenanceDate'])) {
				// 	$this->saveMaintenancesJoins($this->data['SecurityServiceMaintenanceDate'], $this->id);
				// }
			}

			// $this->resaveNotifications($this->id);

            // when deleted, move to trash also it's associated audits and maintenances
	        if (!$this->exists($this->id)) {
	        	// $ret &= $this->_relatedObjectsRemoval('SecurityServiceAudit', $this->id);
	        	// $ret &= $this->_relatedObjectsRemoval('SecurityServiceMaintenance', $this->id);
	        }
		}

		//$ret &= $this->logMappedProjects();

		
		// $ret &= $this->resaveNotifications($this->id);
		
		// Add new and remove unused Audits and Maintenances for next year
		$this->updateNextYearAuditsAndMaintenances();

		return $ret;
	}

	protected function updateNextYearAuditsAndMaintenances()
	{
		if (empty($this->id)) {
			return false;
		}

		$auditDates = isset($this->data['SecurityServiceAuditDate']) ? $this->data['SecurityServiceAuditDate'] : [];
		$maintenanceDates = isset($this->data['SecurityServiceMaintenanceDate']) ? $this->data['SecurityServiceMaintenanceDate'] : [];

		//
		// Delete previosly added Audits and Maintenances for next year which are not needed anymore
		$existingAudits = $this->SecurityServiceAudit->find('all', [
			'conditions' => [
				'security_service_id' => $this->id
			],
			'recursive' => -1
		]);
		$existingMaintenances = $this->SecurityServiceMaintenance->find('all', [
			'conditions' => [
				'security_service_id' => $this->id
			],
			'recursive' => -1
		]);

		$this->SecurityServiceAudit->softDelete(false);
		foreach ($existingAudits as $item) {
			$plannedDate = $item['SecurityServiceAudit']['planned_date'];
			if ($this->isNextYearDate($plannedDate) &&
				!$this->dateExists($plannedDate, $auditDates)) {
				$this->SecurityServiceAudit->delete($item['SecurityServiceAudit']['id']);
			}
		}
		$this->SecurityServiceAudit->softDelete(true);

		$this->SecurityServiceMaintenance->softDelete(false);
		foreach ($existingMaintenances as $item) {
			$plannedDate = $item['SecurityServiceMaintenance']['planned_date'];
			if ($this->isNextYearDate($plannedDate) &&
				!$this->dateExists($plannedDate, $maintenanceDates)) {
				$this->SecurityServiceMaintenance->delete($item['SecurityServiceMaintenance']['id']);
			}
		}
		$this->SecurityServiceMaintenance->softDelete(true);
		// 

		//
		// Add new Audits and Maintenances for next year
		// 
		// Temporary save data so they can be set again (in some cases data are removed becouse some other functionality)
		$tempData = $this->data;

		$this->saveAuditsJoins($auditDates, $this->data['SecurityService']['id'], true);

		// Set previously saved data back to this model
		$this->set($tempData);

		$this->saveMaintenancesJoins($maintenanceDates, $this->data['SecurityService']['id'], true);
		//
	}

	protected function isNextYearDate($date)
	{
		$isNextYearDate = false;
		$nextYear = date('Y', strtotime(date('Y') . " + 365 day"));
		$dateYear = date('Y', strtotime($date));

		if ($dateYear === $nextYear) {
			$isNextYearDate = true;
		}

		return $isNextYearDate;
	}

	protected function dateExists($date, $dates)
	{
		$dateExists = false;
		$dateMonth = date('n', strtotime($date));
		$dateDay = date('j', strtotime($date));

		foreach ($dates as $d) {
			if (intval($d['month']) == intval($dateMonth) &&
				intval($d['day']) == intval($dateDay)) {
				$dateExists = true;
				break;
			}
		}

		return $dateExists;
	}

	protected function _relatedObjectsRemoval($relatedModel, $id) {
		$related = $this->{$relatedModel}->find('list', [
    		'conditions' => [
    			'security_service_id' => $this->id
    		],
    		'recursive' => -1
    	]);

		$ret = true;
    	foreach (array_keys($related) as $id) {
    		$ret &= $this->{$relatedModel}->delete($id);
    	}

    	return $ret;
	}

	/**
	 * If a control status is set to design - delete all joins.
	 */
	public function setEmptyPoductionJoins() {
		$this->data['SecurityService']['SecurityIncident'] = array();
		$this->data['SecurityService']['DataAsset'] = array();
		$this->data['SecurityService']['ComplianceManagement'] = array();
	}

	/**
	 * @deprecated
	 */
	public function saveJoins($data = null) {
		$this->data = $data;

		$ret = true;

		$ret &= $this->joinHabtm('ServiceContract', 'service_contract_id');
		$ret &= $this->joinHabtm('SecurityPolicy', 'security_policy_id');
		$ret &= $this->joinHabtm('Project', 'project_id');
		//$ret &= $this->joinHabtm('Collaborators', 'collaborator_id');

		if (!empty($this->data['SecurityService']['Classification'])) {
			$ret &= $this->saveClassifications($this->data['SecurityService']['Classification'], $this->id);
		}

		// if ( isset( $this->data['SecurityService']['audit_calendar'] ) && ! empty( $this->data['SecurityService']['audit_calendar'] ) ) {
		// 	$ret &= $this->saveAuditDates( $this->data['SecurityService']['audit_calendar'], $this->id );
		// 	$ret &= $this->saveAuditsJoins( $this->data['SecurityService']['audit_calendar'], $this->id );

		// 	//temporarily reassign data because it gets lost during audit joins
		// 	$this->data = $data;
		// }

		// if ( isset( $this->data['SecurityService']['maintenance_calendar'] ) && ! empty( $this->data['SecurityService']['maintenance_calendar'] ) ) {
		// 	$ret &= $this->saveMaintenanceDates( $this->data['SecurityService']['maintenance_calendar'], $this->id );
		// 	$ret &= $this->saveMaintenancesJoins( $this->data['SecurityService']['maintenance_calendar'], $this->id );

		// 	//temporarily reassign data because it gets lost during audit joins
		// 	$this->data = $data;
		// }

		$this->data = false;
		
		return $ret;
	}

	/**
	 * @deprecated
	 * 
	 * delete all hasMany data
	 * 
	 * @param  int $id security_service_id
	 * @return boolean 
	 */
	public function deleteJoins($id) {
		// $ret = $this->SecurityServicesServiceContract->deleteAll( array(
		// 	'SecurityServicesServiceContract.security_service_id' => $id
		// ) );
		// $ret &= $this->SecurityPoliciesSecurityService->deleteAll( array(
		// 	'SecurityPoliciesSecurityService.security_service_id' => $id
		// ) );
		// $ret = $this->SecurityServiceAuditDate->deleteAll( array(
		// 	'SecurityServiceAuditDate.security_service_id' => $id
		// ) );
		// $ret &= $this->SecurityServiceMaintenanceDate->deleteAll( array(
		// 	'SecurityServiceMaintenanceDate.security_service_id' => $id
		// ) );
		// $ret &= $this->ProjectsSecurityServices->deleteAll(array(
		// 	'ProjectsSecurityServices.security_service_id' => $id
		// ) );
		// $ret &= $this->SecurityServicesUser->deleteAll(array(
		// 	'SecurityServicesUser.security_service_id' => $id
		// ) );
		$ret = $this->Classification->deleteAll(array(
			'Classification.security_service_id' => $id
		) );


		return $ret;
	}

	public function convertClassificationsImport($value) {
		if (is_array($value)) {
			return $value;
		}

		return false;
	}

	// conversion of audit dates for import tool export
	public function convertAuditDateExport($item) {
		return $this->_convertDatesImportTool($item, 'SecurityServiceAuditDate');
	}

	// conversion of maintenance dates for import tool export
	public function convertMaintenanceDateExport($item) {
		return $this->_convertDatesImportTool($item, 'SecurityServiceMaintenanceDate');
	}

	/**
	 * Generic method that makes a conversion of Audit or Maintenance dates for import tool export.
	 */
	protected function _convertDatesImportTool($item, $model) {
		if (!in_array($model, ['SecurityServiceAuditDate', 'SecurityServiceMaintenanceDate'])) {
			trigger_error('Wrong model for conversion entered');
		}

		if (!empty($item[$model])) {
			$dates = [];
			foreach ($item[$model] as $date) {
				$dates[] = $date['day'] . '-' . $date['month'];
			}

			return ImportToolModule::buildValues($dates);
		}

		return false;
	}

	// convert dates for import
	public function convertAuditDateImport($dates) {
		if (!empty($dates)) {
			$data = [];
			foreach ($dates as $date) {
				$exploded = explode('-', $date);
				$data[] = [
					'day' => isset($exploded[0]) ? $exploded[0] : false,
					'month' => isset($exploded[1]) ? $exploded[1] : false
				];
			}

			return $data;
		}

		return false;
	}

	/**
	 * save Classification associated with SecurityService
	 * 
	 * @param  string $labels comma-separated classifications
	 * @param  int $id security_service_id
	 * @return boolean $result
	 */
	private function saveClassifications($labels, $id) {
		if (empty($labels)) {
			return true;
		}

		$data = array();
		foreach ($labels as $name) {
			$data[] = array(
				'security_service_id' => $id,
				'name' => $name
			);
		}

		$result = $this->Classification->saveAll($data, array(
			'validate' => false,
			'atomic' => false
		));

		return (bool) $result;
	}

	/**
	 * save hasMany SecurityServiceAuditDate associated with SecurityService
	 * 
	 * @param  array $list list of dates
	 * @param  int $service_id
	 * @return boolean $result
	 */
	private function saveAuditDates($list, $service_id) {
		$data = array();

		foreach ($list as $date) {
			$data[] = array(
				'security_service_id' => $service_id,
				'day' => $date['day'],
				'month' => $date['month']
			);
		}

		$result = $this->SecurityServiceAuditDate->saveAll($data, array(
			'validate' => false,
			'atomic' => false
		));

		return (bool) $result;
	}

	/**
	 * save SecurityServiceAudit data related to SecurityService
	 * 
	 * @param  array $list list of dates
	 * @param  int $service_id
	 * @return boolean $result
	 */
	public function saveAuditsJoins($list, $service_id, $nextYear = false)
	{
		$user = $this->currentUser();
		$data = array();
		$year = date('Y');
		if ($nextYear) {
			$year = date('Y', strtotime(date('Y') . " + 365 day"));
		}
		
		foreach ($list as $date) {
			$dataItem = array(
				'security_service_id' => $service_id,
				'planned_date' =>  $year . '-' . $date['month'] . '-' . $date['day'],
				'AuditOwner' => $this->getStoredOldModelData('AuditOwner'),
				'AuditEvidenceOwner' => $this->getStoredOldModelData('AuditEvidenceOwner'),
				'audit_metric_description' => $this->data['SecurityService']['audit_metric_description'],
				'audit_success_criteria' => $this->data['SecurityService']['audit_success_criteria'],
			);

			$secServAuditData = $this->SecurityServiceAudit->find('all', array(
				'fields' => array(
					'SecurityServiceAudit.id',
					'SecurityServiceAudit.planned_date'
				),
				'conditions' => array(
					'SecurityServiceAudit.security_service_id' => $service_id,
					'SecurityServiceAudit.planned_date' => $year . '-' . $date['month'] . '-' . $date['day']
				),
				'recursive' => -1
			));

			if (empty($secServAuditData)) {
				$data[] = $dataItem;
			} /*elseif (!empty($dataItem['user_id'])) {
				foreach ($secServAuditData as $ssad) {
					if (empty($ssad['SecurityServiceAudit']['planned_date']) || 
					strtotime($ssad['SecurityServiceAudit']['planned_date']) >= strtotime(date('Y-m-d', time()))) {
						$data[] = array(
							'id' => $ssad['SecurityServiceAudit']['id'],
							'user_id' => $dataItem['user_id']
						);
					}
				}
			}*/
		}

		if (empty($data)) {
			return true;
		}

		$result = $this->SecurityServiceAudit->saveAll($data, array(
			'validate' => false,
			'atomic' => false,
		));

		return (bool) $result;
	}

	/**
	 * save hasMany SecurityServiceMaintenanceDate associated to SecurityService
	 * 
	 * @param  array $list list of dates
	 * @param  int $service_id
	 * @return boolean $result
	 */
	private function saveMaintenanceDates( $list, $service_id ) {
		$data = array();

		foreach ($list as $date) {
			$data[] = array(
				'security_service_id' => $service_id,
				'day' => $date['day'],
				'month' => $date['month']
			);
		}

		$result = $this->SecurityServiceMaintenanceDate->saveAll($data, array(
			'validate' => false,
			'atomic' => false
		));

		return (bool) $result;
	}

	/**
	 * save SecurityServiceMaintenance data related to SecurityService
	 * 
	 * @param  array $list list of dates
	 * @param  int $service_id
	 * @return boolean $result
	 */
	public function saveMaintenancesJoins($list, $service_id, $nextYear = false)
	{
		$data = array();
		$year = date('Y');
		if ($nextYear) {
			$year = date('Y', strtotime(date('Y') . " + 365 day"));
		}

		foreach ($list as $date) {
			$dataItem = array(
				'security_service_id' => $service_id,
				'planned_date' =>  $year . '-' . $date['month'] . '-' . $date['day'],
				'MaintenanceOwner' => $this->getStoredOldModelData('MaintenanceOwner'),
				'task' => $this->data['SecurityService']['maintenance_metric_description']
			);

			$secServMtncData = $this->SecurityServiceMaintenance->find('all', array(
				'fields' => array(
					'SecurityServiceMaintenance.id',
					'SecurityServiceMaintenance.planned_date'
				),
				'conditions' => array(
					'SecurityServiceMaintenance.security_service_id' => $service_id,
					'SecurityServiceMaintenance.planned_date' => $year . '-' . $date['month'] . '-' . $date['day']
				),
				'recursive' => -1
			));

			if (empty($secServMtncData)) {
				$data[] = $dataItem;
			} /*elseif (!empty($dataItem['user_id'])) {
				foreach ($secServMtncData as $ssmd) {
					if (empty($ssmd['SecurityServiceMaintenance']['planned_date']) || 
					strtotime($ssmd['SecurityServiceMaintenance']['planned_date']) >= strtotime(date('Y-m-d', time()))) {
						$data[] = array(
							'id' => $ssmd['SecurityServiceMaintenance']['id'],
							'user_id' => $dataItem['user_id']
						);
					}
				}
			}*/
		}

		if (empty($data)) {
			return true;
		}

		$result = $this->SecurityServiceMaintenance->saveAll($data, array(
			'validate' => false,
			'atomic' => false,
		));

		return true;
	}

	public function resaveNotifications($id) {
		$ret = true;

		// $this->bindNotifications();
		// $ret &= $this->NotificationObject->NotificationSystem->saveCustomUsersByModel($this->alias, $id);

		$auditIds = $this->SecurityServiceAudit->find('list', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		// $this->SecurityServiceAudit->bindNotifications();
		// $ret &= $this->SecurityServiceAudit->NotificationObject->NotificationSystem->saveCustomUsersByModel('SecurityServiceAudit', $auditIds);

		return $ret;
	}

	public function statusProcess($id, $column) {
		if ($column == 'audits_last_passed' || $column == 'audits_last_missing') {
			$statuses = $this->SecurityServiceAudit->getStatuses($id);
		}

		if ($column == 'maintenances_last_missing') {
			$statuses = $this->SecurityServiceMaintenance->getStatuses($id);
		}

		return $statuses[$column];
	}

	/**
	 * @deprecated status, in favor of SecurityService::_statusOngoingCorrectiveActions()
	 */
	public function statusOngoingCorrectiveActions($id) {
		$this->ProjectsSecurityService->bindModel(array(
			'belongsTo' => array('Project')
		));

		// check projects by status
		$ret = $this->ProjectsSecurityService->find('count', array(
			'conditions' => array(
				'ProjectsSecurityService.security_service_id' => $id,
				'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED
			),
			'recursive' => 0
		));

		$auditIds = $this->SecurityServiceAudit->find('list', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		$Improvement = $this->SecurityServiceAudit->SecurityServiceAuditImprovement;

		// or also check projects associated to audit improvements by status
		$ret = $ret || $Improvement->ProjectsSecurityServiceAuditImprovement->find('count', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id,
				'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED
			),
			'joins' => $Improvement->getRelatedJoins(),
			'recursive' => -1
		));

		// if at least 1 record was found that means it should show ongoing corrective actions status
		if ($ret) {
			return 1;
		}

		return 0;
	}

	/**
	 * @deprecated status, in favor of SecurityService::statusControlWithIssues()
	 */
	public function statusHasIssues($id) {
		$count = $this->Issue->find('count', array(
			'conditions' => array(
				'Issue.model' => 'SecurityService',
				'Issue.foreign_key' => $id,
				'Issue.status' => ISSUE_OPEN
			),
			'recursive' => -1
		));

		if ($count) {
			return 1;
		}

		return 0;
	}

	/*public function mappedProjects($id) {
		$data = $this->ProjectsSecurityServices->find('list', array(
			'conditions' => array(
				'ProjectsSecurityServices.security_service_id' => $id
			),
			'fields' => array('id', 'project_id'),
			'recursive' => -1
		));

		if (empty($data)) {
			return false;
		}

		$projects = $this->Projects->find('list', array(
			'conditions' => array(
				'Projects.id' => $data
			),
			'fields' => array('id', 'title'),
			'recursive' => -1
		));

		return implode(', ', $projects);
	}*/

	/*public function lastAuditDate($id, $result = array(1, null), $field = 'planned_date') {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->SecurityServiceAudit->find('first', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id,
				'SecurityServiceAudit.planned_date <=' => $today,
				'SecurityServiceAudit.result' => $result
			),
			'fields' => array('SecurityServiceAudit.id', 'SecurityServiceAudit.result', 'SecurityServiceAudit.planned_date'),
			'order' => array('SecurityServiceAudit.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			return $audit['SecurityServiceAudit'][$field];
		}

		return false;
	}*/

	/*public function lastMissingAudit($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->SecurityServiceAudit->find('first', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id,
				'SecurityServiceAudit.planned_date <=' => $today,
				'SecurityServiceAudit.result' => null
			),
			'order' => array('SecurityServiceAudit.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			$this->lastMissingAuditId = $audit['SecurityServiceAudit']['id'];
			return $audit['SecurityServiceAudit']['planned_date'];
		}

		return false;
	}

	public function lastMissingAuditResult($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->SecurityServiceAudit->find('first', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $id,
				'SecurityServiceAudit.planned_date <=' => $today,
				'SecurityServiceAudit.result' => array(1,0)
			),
			'order' => array('SecurityServiceAudit.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			if ($audit['SecurityServiceAudit']['result']) {
				return __('Pass');
			}

			return __('Fail');

		}

		return false;
	}*/

	public function lastMissingMaintenance($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$Maintenance = $this->SecurityServiceMaintenance->find('first', array(
			'conditions' => array(
				'SecurityServiceMaintenance.security_service_id' => $id,
				'SecurityServiceMaintenance.planned_date <=' => $today,
				'SecurityServiceMaintenance.result' => null
			),
			'order' => array('SecurityServiceMaintenance.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($Maintenance)) {
			return $Maintenance['SecurityServiceMaintenance']['planned_date'];
		}

		return false;
	}

	public function lastMissingMaintenanceResult($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$Maintenance = $this->SecurityServiceMaintenance->find('first', array(
			'conditions' => array(
				'SecurityServiceMaintenance.security_service_id' => $id,
				'SecurityServiceMaintenance.planned_date <=' => $today,
				'SecurityServiceMaintenance.result' => array(1,0)
			),
			'order' => array(/*'SecurityServiceMaintenance.planned_date' => 'DESC', */'SecurityServiceMaintenance.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($Maintenance)) {
			if ($Maintenance['SecurityServiceMaintenance']['result']) {
				return __('Pass');
			}

			return __('Fail');

		}

		return false;
	}

	public function checkLastAuditStatus($id, $param) {
		if (empty($id) || empty($param)) {
			return null;
		}

		$audits = $this->SecurityServiceAudit->getStatuses($id);
		return $audits[$param];
	}

	/**
	 * Update audits and maintenances fields (config for updateAuditsAndMaintenancesFields method)
	 * @return bool True if success, false if failure
	 */
	private function updateAuditsAndMaintenances()
	{
		$ret = true;
		$ret &= $this->updateAuditsAndMaintenancesFields('SecurityServiceAudit', [
				'audit_metric_description' => 'audit_metric_description',
				'audit_success_criteria' => 'audit_success_criteria'
			],
			[
				'AuditOwner' => 'AuditOwner',
				'AuditEvidenceOwner' => 'AuditEvidenceOwner'
			],
			[
				'result' => null,
				'security_service_id' => $this->id
			]);
		$ret &= $this->updateAuditsAndMaintenancesFields('SecurityServiceMaintenance', [
				'maintenance_metric_description' => 'task'
			],
			[
				'MaintenanceOwner' => 'MaintenanceOwner'
			],
			[
				'result' => null,
				'security_service_id' => $this->id
			]);

		return $ret;
	}

	/**
	 * Update audits and maintenances fields
	 * @param string $assocModel   Name of model associated to this model (audit or maintenance model)
	 * @param array  $staticFields Current model field => Assoc Model field (use for any fields which belongs to current model)
	 * @param array  $userFields   Current model field => Assoc Model field (use only for UserFields)
	 * @param array  $conditions   Conditions for find which will get all items which need to be updated in associated model
	 * @return bool                True if success, false if failure
	 */
	private function updateAuditsAndMaintenancesFields($assocModel, $staticFields, $userFields, $conditions)
	{
		if (empty($this->id)) {
			return false;
		}

		$data = $this->find('first', [
			'conditions' => [
				'SecurityService.id' => $this->id
			]
		]);

		if (empty($data)) {
			return false;
		}

		//
		// Prepare current model's fields for update
		$updateFields = [];
		foreach ($staticFields as $cm_field => $am_field) {
			if (isset($this->data[$this->name][$cm_field]) && $this->data[$this->name][$cm_field] !== $data[$this->name][$cm_field]) {
				$updateFields[$am_field] = $this->data[$this->name][$cm_field];
			}
		}
		//
		
		//
		// Prepare UserFields for update
		foreach ($userFields as $cm_field => $am_field) {
			// Get old user field data
			$userFieldOld = Hash::extract($data, "{$cm_field}.{n}.id");

			// Get new user field data
			$userFieldNew = [];
			$userFieldUser = Hash::get($this->data, "{$cm_field}.{$cm_field}", []);
			$userFieldGroup = Hash::get($this->data, "{$cm_field}Group.{$cm_field}Group", []);
			foreach ($userFieldUser as $uFieldUser) {
				if (isset($uFieldUser['user_id'])) {
					$userFieldNew[] = UserFieldsBehavior::getUserIdPrefix() . $uFieldUser['user_id'];
				}
			}
			foreach ($userFieldGroup as $uFieldGroup) {
				if (isset($uFieldGroup['group_id'])) {
					$userFieldNew[] = UserFieldsBehavior::getGroupIdPrefix() . $uFieldGroup['group_id'];
				}
			}

			if ((count($userFieldNew) != count($userFieldOld)) ||
				!empty(array_diff($userFieldNew, $userFieldOld))) {
				$updateFields[$am_field] = $userFieldNew;
			}
		}
		//

		//
		// Update prepared fields
		if (!empty($updateFields)) {
			foreach ($conditions as $key => $val) {
				if (strpos($val, '.') === false) {
					unset($conditions[$key]);
					$conditions[$assocModel . '.' . $key] = $val;
				}
			}

			$updateIds = $this->{$assocModel}->find('all', [
				'fields' => [
					"{$assocModel}.id"
				],
				'conditions' => $conditions,
				'recursive' => -1
			]);

			$fieldsForUpdate = [];
			foreach ($updateIds as $uid) {
				$id = Hash::get($uid, $assocModel . '.id', false);
				if ($id === false) {
					continue;
				}

				$updateFields['id'] = $id;
				$fieldsForUpdate[] = $updateFields;
			}

			if (!empty($fieldsForUpdate)) {
				return $this->{$assocModel}->saveMany($fieldsForUpdate, ['validate' => false, 'deep' => true]);
			}
		}
		//
		
		return false;
	}

	private function disableDesignValidation() {
		$disableValidation = false;
		if (isset($this->data['SecurityService']['security_service_type_id'])) {
			if ($this->data['SecurityService']['security_service_type_id'] == SECURITY_SERVICE_DESIGN) {
				$disableValidation = true;
			}
		}
		elseif ($this->id != null) {
			$data = $this->find('count', array(
				'conditions' => array(
					'id' => $this->id,
					'security_service_type_id' => SECURITY_SERVICE_DESIGN
				),
				'recursive' => -1
			));

			if (!empty($data)) {
				$disableValidation = true;
			}
		}

		if ($disableValidation) {
			$this->validator()->remove('audit_metric_description');
			$this->validator()->remove('audit_success_criteria');
			$this->validator()->remove('maintenance_metric_description');
		}
	}

	public function getSecurityServiceTypes() {
		if (isset($this->data['SecurityService']['security_service_type_id'])) {
			$type = $this->SecurityServiceType->find('first', array(
				'conditions' => array(
					'SecurityServiceType.id' => $this->data['SecurityService']['security_service_type_id']
				),
				'fields' => array('name'),
				'recursive' => -1
			));

			return $type['SecurityServiceType']['name'];
		}

		return false;
	}

	/**
	 * @deprecated
	 */
	public function getLastAuditFailedDate() {
		if (!empty($this->lastAuditFailed)) {
			return $this->lastAuditFailed;
		}

		return false;
	}

	/**
	 * @deprecated
	 */
	public function getLastAuditMissingDate() {
		if (!empty($this->lastAuditMissing)) {
			return $this->lastAuditMissing;
		}

		return false;
	}

	/**
	 * @deprecated
	 */
	public function getLastMaintenanceMissingDate() {
		if (!empty($this->lastMaintenanceMissing)) {
			return $this->lastMaintenanceMissing;
		}

		return false;
	}

	/**
	 * Saves audits and maintenance fields for a security service.
	 * @param  int $id Security Service ID.
	 */
	public function saveAuditsMaintenances($id) {
		// $audits = $this->SecurityServiceAudit->getStatuses($id);
		// $maintenances = $this->SecurityServiceMaintenance->getStatuses($id);

		// $saveData = array_merge($audits, $maintenances);

		// $this->id = $id;
		// $ret = $this->save($saveData, array('validate' => false, 'callbacks' => 'before'));


		// return $ret;

		return true;
	}

	/**
	 * Saves audits fields for a security service.
	 * @param  int $id Security Service ID.
	 */
	public function saveAudits($id, $processType = null) {
		//function is used in AppAudit logic and cannot be removed
		return true;
	}

	/**
	 * Saves audits fields for a security service.
	 * @param  int $id Security Service ID.
	 */
	public function saveMaintenances($id, $processType = null) {
		//function is used in AppAudit logic and cannot be removed
		return true;
	}

	private function getSecurityServiceRisks($id) {
		$data = $this->RisksSecurityService->find('list', array(
			'conditions' => array(
				'RisksSecurityService.security_service_id' => $id
			),
			'fields' => array('RisksSecurityService.risk_id'),
			'recursive' => -1
		));

		return $data;
	}

	private function getSecurityServiceTpRisks($id) {
		$data = $this->SecurityServicesThirdPartyRisk->find('list', array(
			'conditions' => array(
				'SecurityServicesThirdPartyRisk.security_service_id' => $id
			),
			'fields' => array('SecurityServicesThirdPartyRisk.third_party_risk_id'),
			'recursive' => -1
		));

		return $data;
	}

	private function getSecurityServiceBusinessRisks($id) {
		$data = $this->BusinessContinuitiesSecurityService->find('list', array(
			'conditions' => array(
				'BusinessContinuitiesSecurityService.security_service_id' => $id
			),
			'fields' => array('BusinessContinuitiesSecurityService.business_continuity_id'),
			'recursive' => -1
		));

		return $data;
	}

	public function getIssues($id = array(), $find = 'list') {
		if (empty($id)) {
			return false;
		}

		if ($find == 'all') {
			$data = $this->find($find, array(
				'conditions' => array(
					'SecurityService.id' => $id
				),
				'fields' => array(
					'MIN(SecurityService.audits_last_passed) AS LastAuditPassed',
					'MAX(SecurityService.audits_last_missing) AS LastAuditMissing',
					'MAX(SecurityService.maintenances_last_missing) AS LastMaintenanceMissing',
					'MAX(SecurityService.audits_improvements) AS AuditImprovements',
					'SUM(SecurityService.security_incident_open_count) AS OngoingSecurityIncident',
					'MIN(SecurityService.security_service_type_id) AS SecurityServiceTypeId',

				),
				'recursive' => -1
			));

			$data = $data[0][0];
		}
		else {
			$data = $this->find($find, array(
				'conditions' => array(
					'OR' => array(
						array(
							'SecurityService.id' => $id,
							'SecurityService.audits_all_done' => 0
						),
						array(
							'SecurityService.id' => $id,
							'SecurityService.audits_last_passed' => 0
						)
					)
				),
				'fields' => array('SecurityService.id', 'SecurityService.name'),
				'recursive' => 0
			));
		}

		return $data;
	}

	/**
	 * Get associated projects.
	 */
	public function getAssignedProjects($id, $type = 'count') {
		$data = $this->ProjectsSecurityServices->find($type, array(
			'conditions' => array(
				'ProjectsSecurityServices.security_service_id' => $id
			),
			'recursive' => -1
		));

		return $data;
	}

	public function getProjects() {
		return $this->Project->getList();
	}

	/**
	 * @deprecated
	 */
	public function auditsLastPassedConditions($data = array()){
		$conditions = array();
		if($data['audits_last_failed'] == 1){
			$conditions = array(
				'SecurityService.audits_last_passed' => 0
			);
		}
		elseif($data['audits_last_failed'] == 0){
			$conditions = array(
				'SecurityService.audits_last_passed' => 1
			);
		}

		return $conditions;
	}

	/**
	 * @deprecated
	 */
	public function securityIncidentOpenConditions($data = array()){
		$conditions = array();
		if($data['security_incident_open_count'] == 1){
			$conditions = array(
				'SecurityService.security_incident_open_count >' => 0
			);
		}
		elseif($data['security_incident_open_count'] == 0){
			$conditions = array(
				'SecurityService.security_incident_open_count' => 0
			);
		}

		return $conditions;
	}

	/**
	 * @deprecated
	 */
	public function designConditions($data = array()){
		$conditions = array();
		if($data['design'] == 1){
			$conditions = array(
				'SecurityServiceType.id' => SECURITY_SERVICE_DESIGN
			);
		}
		elseif($data['design'] == 0){
			$conditions = array(
				'SecurityServiceType.id !=' => SECURITY_SERVICE_DESIGN
			);
		}

		return $conditions;
	}

	public function getClassifications() {
		$tags = $this->Classification->find('list', array(
			'order' => array('Classification.name' => 'ASC'),
			'fields' => array('Classification.name', 'Classification.name'),
			'group' => array('Classification.name'),
			'recursive' => -1
		));

		return $tags;
	}

	public function findByClassifications($data = array(), $filterParams = array()) {
		// $this->Classification->Behaviors->attach('Containable', array(
		// 		'autoFields' => false
		// 	)
		// );
		// $this->Classification->Behaviors->attach('Search.Searchable');
		
		// $query = $this->Classification->getQuery('all', array(
		// 	'conditions' => array(
		// 		'Classification.name' => $data['classifications'],
		// 	),
		// 	'fields' => array(
		// 		'Classification.security_service_id'
		// 	),
		// 	'group' => array(
		// 		'Classification.security_service_id HAVING COUNT(Classification.security_service_id) = ' . count($data['classifications'])
		// 	),
		// 	'recursive' => -1
		// ));

		// return $query;
		// debug($query);exit;

		$this->Classification->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->Classification->Behaviors->attach('Search.Searchable');

		$query = $this->Classification->getQuery('all', array(
			'conditions' => array(
				'Classification.name' => $data['classifications']
			),
			// 'group' => array('Classification.name'),
			'fields' => array(
				'Classification.security_service_id'
			)
		));
		// debug($query);exit;

		return $query;
	}

	public function findByAuditDate($data = array(), $filter) {
		$this->SecurityServiceAudit->Behaviors->attach('Containable', array('autoFields' => false));
		$this->SecurityServiceAudit->Behaviors->attach('Search.Searchable');

		$query = $this->SecurityServiceAudit->getQuery('all', array(
			'conditions' => array(
				'SecurityServiceAudit.planned_date ' .  getComparisonTypes()[$filter['comp_type']] => $data['security_service_audit_date']
			),
			'fields' => array(
				'SecurityServiceAudit.security_service_id'
			),
			'contain' => array()
		));

		return $query;
	}

	public function findByMaintanceDate($data = array(), $filter) {
		$this->SecurityServiceMaintenance->Behaviors->attach('Containable', array('autoFields' => false));
		$this->SecurityServiceMaintenance->Behaviors->attach('Search.Searchable');

		$query = $this->SecurityServiceMaintenance->getQuery('all', array(
			'conditions' => array(
				'SecurityServiceMaintenance.planned_date ' .  getComparisonTypes()[$filter['comp_type']] => $data['security_service_maintance_date']
			),
			'fields' => array(
				'SecurityServiceMaintenance.security_service_id'
			),
			'contain' => array()
		));

		return $query;
	}

	public function findByCompliancePackage($data = array(), $filterParams = array()) {
		$this->ComplianceManagementsSecurityService->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->ComplianceManagementsSecurityService->Behaviors->attach('Search.Searchable');

		$value = $data[$filterParams['name']];

		$joins = array(
			array(
				'table' => 'compliance_managements',
				'alias' => 'ComplianceManagement',
				'type' => 'LEFT',
				'conditions' => array(
					'ComplianceManagementsSecurityService.compliance_management_id = ComplianceManagement.id'
				)
			),
			array(
				'table' => 'compliance_package_items',
				'alias' => 'CompliancePackageItem',
				'type' => 'LEFT',
				'conditions' => array(
					'ComplianceManagement.compliance_package_item_id = CompliancePackageItem.id'
				)
			),
		);

		$conditions = array();
		if ($filterParams['findByField'] == 'ThirdParty.id') {
			$conditions = array(
				$filterParams['findByField'] => $value
			);
			$joins[] = array(
				'table' => 'compliance_packages',
				'alias' => 'CompliancePackage',
				'type' => 'LEFT',
				'conditions' => array(
					'CompliancePackageItem.compliance_package_id = CompliancePackage.id'
				)
			);
			$joins[] = array(
				'table' => 'third_parties',
				'alias' => 'ThirdParty',
				'type' => 'LEFT',
				'conditions' => array(
					'ThirdParty.id = CompliancePackage.third_party_id'
				)
			);
		}
		else {
			$conditions = array(
				$filterParams['findByField'] . ' LIKE' => '%' . $value . '%'
			);
		}

		$query = $this->ComplianceManagementsSecurityService->getQuery('all', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => array(
				'ComplianceManagementsSecurityService.security_service_id'
			),
			// 'group' => 'ThirdParty.id'
		));

		return $query;
	}

	public function findByMaintenanceTask($data = array()) {
		$this->SecurityServiceMaintenance->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->SecurityServiceMaintenance->Behaviors->attach('Search.Searchable');

		$query = $this->SecurityServiceMaintenance->getQuery('all', array(
			'conditions' => array(
				'SecurityServiceMaintenance.task LIKE' => '%' . $data['maintenance_task'] . '%'
			),
			'fields' => array(
				'SecurityServiceMaintenance.security_service_id'
			),
			'recursive' => -1
		));

		return $query;
	}

	public function getThirdParties() {
		return $this->ComplianceManagement->getThirdParties();
	}

	public function getSecurityPolicyIds($securityServiceIds = array()) {
		$securityPolicyIds = $this->SecurityPoliciesSecurityService->find('list', array(
			'conditions' => array(
				'SecurityPoliciesSecurityService.security_service_id' => $securityServiceIds
			),
			'fields' => array(
				'SecurityPoliciesSecurityService.security_policy_id'
			)
		));

		return array_values($securityPolicyIds);
	}

	public function hasSectionIndex()
	{
		return true;
	}

}
