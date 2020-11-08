<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
App::uses('UserFields', 'UserFields.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('ImportToolModule', 'ImportTool.Lib');

class SecurityIncident extends AppModel
{
	public $displayField = 'title';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'title',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false,
	);

	public $workflow = array(
		// 'pullWorkflowData' => array('SecurityIncidentStagesSecurityIncident')
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'description', 'reporter', 'victim', 'open_date', 'closure_date', 'type', 'security_incident_status_id', 'security_incident_classification_id', 'lifecycle_incomplete'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => ['Owner']
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
		'CustomLabels.CustomLabels',
	);

	public $validate = array(
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		/*'security_service_id' => array(
			'rule' => array( 'multiple', array( 'min' => 1 ) )
		),*/
		'open_date' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field cannot be left blank'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Please enter a valid date'
			)
		),
		'closure_date' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field cannot be left blank'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Please enter a valid date'
			)
		)
	);

	public $belongsTo = array(
		'SecurityIncidentStatus',
		/*'ThirdParty' => array(
			'counterCache' => array(
				'security_incident_count' => true,
				'security_incident_open_count' => array(
					'SecurityIncident.security_incident_status_id' => SECURITY_INCIDENT_ONGOING
				)
			)
		),*/
		/*'Asset' => array(
			'counterCache' => array(
				'security_incident_open_count' => array(
					'SecurityIncident.security_incident_status_id' => SECURITY_INCIDENT_ONGOING
				)
			)
		),*/
	);

	public $hasMany = array(
		'Classification' => array(
			'className' => 'SecurityIncidentClassification'
		),
		'SecurityIncidentStagesSecurityIncident'
	);

	public $hasAndBelongsToMany = array(
		'SecurityService' => array(
			'className' => 'SecurityService',
			'with' => 'SecurityIncidentsSecurityService'
		),
		'AssetRisk' => array(
			'className' => 'Risk',
			'with' => 'RisksSecurityIncident',
			'joinTable' => 'risks_security_incidents',
			'foreignKey' => 'security_incident_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityIncident.risk_type' => 'asset-risk'
			)
		),
		'ThirdPartyRisk' => array(
			'className' => 'ThirdPartyRisk',
			'with' => 'RisksSecurityIncident',
			'joinTable' => 'risks_security_incidents',
			'foreignKey' => 'security_incident_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityIncident.risk_type' => 'third-party-risk'
			)
		),
		'BusinessContinuity' => array(
			'className' => 'BusinessContinuity',
			'with' => 'RisksSecurityIncident',
			'joinTable' => 'risks_security_incidents',
			'foreignKey' => 'security_incident_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityIncident.risk_type' => 'business-risk'
			)
		),
		'Asset',
		'ThirdParty',
		'SecurityIncidentStage'
	);

	public $findContain = array(
		'ThirdParty' => array(
			'fields' => array('name'),
			'Legal' => array(
				'fields' => array('name')
			)
		),
		'Asset' => array(
			'fields' => array('id', 'name', 'description'),
			'Legal' => array(
				'fields' => array('name')
			)
		),
		'Classification',
		'SecurityIncidentStatus' => array(
			'fields' => array('name')
		),
		'SecurityIncidentStage',
		'SecurityIncidentStagesSecurityIncident' => array(
			'Attachment',
			'Comment',
		),
		'AssetRisk' => array(
			'fields' => array('*'),
			'SecurityPolicy' => array(
				'fields' => array('id', 'index', 'use_attachments', 'url')
			)
		),
		'ThirdPartyRisk' => array(
			'fields' => array('*'),
			'SecurityPolicy' => array(
				'fields' => array('id', 'index', 'use_attachments', 'url')
			)
		),
		'BusinessContinuity' => array(
			'fields' => array('*'),
			'SecurityPolicy' => array(
				'fields' => array('id', 'index', 'use_attachments', 'url')
			)
		),
		'Comment',
		'Attachment',
		'CustomFieldValue'
	);

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function types($value = null) {
        $options = array(
            self::TYPE_EVENT => __('Event'),
            self::TYPE_POSSIBLE_INCIDENT => __('Possible Incident'),
            self::TYPE_INCIDENT => __('Incident'),
        );
        return parent::enum($value, $options);
    }

    const TYPE_EVENT = 'event';
    const TYPE_POSSIBLE_INCIDENT = 'possible-incident';
    const TYPE_INCIDENT = 'incident';

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_ONGOING => __('Ongoing'),
            self::STATUS_CLOSED => __('Closed'),
        );
        return parent::enum($value, $options);
    }

    const STATUS_ONGOING = 2;
    const STATUS_CLOSED = 3;

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Security Incident');
		$this->_group = 'security-operations';

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
			'risk-profile' => array(
				'label' => __('Risk Profile')
			),
			'incident-stakeholders' => array(
				'label' => __('Incident Stakeholders')
			),
			'incident-profile' => array(
				'label' => __('Incident Profile')
			)
		);

		$this->fieldData = [
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Give the Security Incident a title, name or code so it\'s easily identified on the menu.')
			],
			'type' => [
				'label' => __('Type'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => [$this, 'types'],
				'description' => __('Incidents can be potential incidents or confirmed incidents. This usually is defined as the incident is investigated and confirmed.')
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the Security Incident in detail (when, what, where, why, whom, how). You will later update the incident using lifecycle stages or comments on the incident itself.'),
				'validates' => [
					'mandatory' => false
				]
			],
			'Classification' => array(
                'label' => __('Tags'),
				'editable' => true,
				'options' => [$this, 'getClassifications'],
				'type' => 'tags',
				'description' => __('Tag this incident according to their characteristics. This can later be useful to apply filters and export data.'),
				'empty' => __('Add a tag'),
				'validates' => [
					'mandatory' => false
				],
				'macro' => [
					'name' => 'tag'
				]
            ),
			'open_date' => [
				'label' => __('Open Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Set the date this incident was reported.')
			],
			'closure_date' => [
				'label' => __('Closure Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('If stages have not been defined (Sec. Operations / Sec. Incidents / Settings / Stages), this field is mandatory only if "Status" is "Closed"
    If stages are defined and you checkbox to "Automatically Close" then the closure date will be updated once all states are completed, you can leave this box empty'),
				'validates' => [
					'mandatory' => false
				],
				'renderHelper' => ['SecurityIncidents', 'closureDateField']
			],
			'security_incident_status_id' => [
				'label' => __('Status'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('For the time the incident is being managed (investigated, communicated, etc.) the incident status should be open. Otherwise it could be closed.'),
				'macro' => [
					'name' => 'status'
				]
			],
			'auto_close_incident' => [
				'label' => __('Automatically Close Incident'),
				'editable' => false,
				'type' => 'toggle',
				'default' => true,
				'description' => __('When all items on the lifecycle are completed this incident will change to "Closed" status automatically.'),
				'renderHelper' => ['SecurityIncidents', 'autoCloseIncidentField'],
				'hidden' => true
			],
			'AssetRisk' => [
				'label' => __('Related Asset Risks'),
				'editable' => true,
				'group' => 'risk-profile',
				'description' => __('If a Risk was previously documented (Risk Management / Asset Risk Management) describing a scenario where this incident could happen, select it in order to include further documentation on this incident (policies to be followed, controls used, assets affected, Etc).'),
				'validates' => [
					'mandatory' => false
				],
				'renderHelper' => ['SecurityIncidents', 'assetRiskField'],
				'quickAdd' => true,
				'inlineEdit' => true,
			],
			'ThirdPartyRisk' => [
				'label' => __('Related Third Party Risks'),
				'editable' => true,
				'group' => 'risk-profile',
				'description' => __('If a Risk was previously documented (Risk Management / Third Party Risk Management) describing a scenario where this incident could happen, select it in order to include further documentation on this incident (policies to be followed, controls used, assets affected, Etc).'),
				'validates' => [
					'mandatory' => false
				],
				'renderHelper' => ['SecurityIncidents', 'thirdPartyRiskField'],
				'quickAdd' => true,
				'inlineEdit' => true,
			],
			'BusinessContinuity' => [
				'label' => __('Related Business Risks'),
				'editable' => true,
				'group' => 'risk-profile',
				'description' => __('If a Risk was previously documented (Risk Management / Business Impact Analysis) describing a scenario where this incident could happen, select it in order to include further documentation on this incident (policies to be followed, controls used, assets affected, Etc).'),
				'validates' => [
					'mandatory' => false
				],
				'renderHelper' => ['SecurityIncidents', 'businessContinuityField'],
				'quickAdd' => true,
				'inlineEdit' => true,
			],
			'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'),
				'group' => 'incident-stakeholders',
				'description' => __('Is the individual in charge of managing the incident'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'reporter' => [
				'label' => __('Reporter'),
				'editable' => true,
				'inlineEdit' => true,
				'group' => 'incident-stakeholders',
				'description' => __('Is the individual that reported the incident, could be the same as the owner. If unknown at the time the incident was reported this field can be updated later.'),
				'validates' => [
					'mandatory' => false
				]
			],
			'victim' => [
				'label' => __('Victim'),
				'editable' => true,
				'inlineEdit' => true,
				'group' => 'incident-stakeholders',
				'description' => __('Is the individual that has been affected by the incident. If unknown at the time the incident was reported this field can be updated later.'),
				'validates' => [
					'mandatory' => false
				]
			],
			'SecurityService' => [
				'label' => __('Affected Compensating Controls'),
				'editable' => true,
				'group' => 'incident-profile',
				'description' => __('Select one or more controls (from Control Catalogue / Security Services) that are involved on this incident. This fields might autocomplete as you select risks (on the previous tab).'),
				'validates' => [
					'mandatory' => false
				],
				'renderHelper' => ['SecurityIncidents', 'securityServiceField'],
				'quickAdd' => true,
				'inlineEdit' => true,
			],
			'Asset' => [
				'label' => __('Affected Asset'),
				'editable' => true,
				'group' => 'incident-profile',
				'description' => __('Select one or more assets (from Asset Managemnet / Asset Identification) that are involved on this incident. This fields might autocomplete as you select risks (on the previous tab).'),
				'validates' => [
					'mandatory' => false
				],
				'renderHelper' => ['SecurityIncidents', 'assetField'],
				'quickAdd' => true,
				'inlineEdit' => true,
			],
			'ThirdParty' => [
				'label' => __('Affected Third Parties'),
				'editable' => true,
				'group' => 'incident-profile',
				'description' => __('Select one or more assets (from Organisation / Third Parties) that are involved on this incident. This fields might autocomplete as you select risks (on the previous tab).'),
				'validates' => [
					'mandatory' => false
				],
				'renderHelper' => ['SecurityIncidents', 'thirdPartyField'],
				'quickAdd' => true,
				'inlineEdit' => true,
			],
			'expired' => [
				'label' => __('Expired'),
				'editable' => false,
				'hidden' => true
			],
			'security_incident_classification_id' => [
				'label' => __('Security Incident Classification'),
				'editable' => false,
				'hidden' => true
			],
			'lifecycle_incomplete' => [
				'label' => __('Lifecycle Incomplete'),
				'editable' => false,
				'hidden' => true
			],
			'ongoing_incident' => [
				'label' => __('Ongoing Incident'),
				'editable' => false,
				'hidden' => true
			],
			'SecurityIncidentStagesSecurityIncident' => [
				'label' => __('Security Incident Stages Security Incident'),
				'editable' => false,
				'hidden' => true
			],
			'SecurityIncidentStage' => [
				'label' => __('Security Incident Stage'),
				'editable' => false,
			],
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Security Incident'),
			'pdf_file_name' => __('security_incidents'),
			'csv_file_name' => __('security_incidents'),
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
			'use_new_filters' => true,
			'add' => true,
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
				->textField('title', [
					'showDefault' => true,
				])
				->textField('description', [
					'showDefault' => true,
				])
				->multipleSelectField('type', [$this, 'types'])
				->multipleSelectField('Classification-name', [$this, 'getClassifications'], [
					'label' => __('Tags')
				])
				->userField('Owner', 'Owner')
				->textField('reporter')
				->textField('victim')
				->dateField('open_date', [
					'showDefault' => true,
				])
				->dateField('closure_date', [
					'showDefault' => true,
				])
				->selectField('security_incident_status_id', [$this, 'statuses'])
				->objectStatusField('ObjectStatus_lifecycle_incomplete', 'lifecycle_incomplete');

		$this->AssetRisk->relatedFilters($advancedFilterConfig)
			->field('AssetRisk', ['showDefault' => true])
			->multipleSelectField('AssetRisk-SecurityPolicyIncident', [ClassRegistry::init('SecurityPolicy'), 'getList'], [
				'label' => __('Asset Risk Incident Procedure'),
				'showDefault' => true,
			]);

		$this->ThirdPartyRisk->relatedFilters($advancedFilterConfig)
			->field('ThirdPartyRisk', ['showDefault' => true])
			->multipleSelectField('ThirdPartyRisk-SecurityPolicyIncident', [ClassRegistry::init('SecurityPolicy'), 'getList'], [
				'label' => __('Third Party Risk Incident Procedure'),
				'showDefault' => true,
			]);

		$this->BusinessContinuity->relatedFilters($advancedFilterConfig)
			->field('BusinessContinuity', ['showDefault' => true])
			->multipleSelectField('BusinessContinuity-SecurityPolicyIncident', [ClassRegistry::init('SecurityPolicy'), 'getList'], [
				'label' => __('Business Risk Incident Procedure'),
				'showDefault' => true,
			]);

		$this->SecurityService->relatedFilters($advancedFilterConfig)
			->field('SecurityService', ['showDefault' => true]);

		$this->Asset->relatedFilters($advancedFilterConfig)
			->field('Asset', ['showDefault' => true]);

		$this->ThirdParty->relatedFilters($advancedFilterConfig)
			->field('ThirdParty', ['showDefault' => true]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
			->group('SecurityIncident', [
				'name' => __('Security Incident')
			])
				->multipleSelectField('SecurityIncident', [$this, 'getList'], [
					'label' => __('Security Incident')
				])
				->selectField('SecurityIncident-security_incident_status_id', [$this, 'statuses'], [
					'label' => __('Security Incident Status')
				]);

		return $advancedFilterConfig;
	}

	public function getImportToolConfig()
	{
		return [
			'SecurityIncident.title' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory, give the Security Incident a title.'),
			],
			'SecurityIncident.type' => [
				'name' => __('Type'),
				'headerTooltip' => __(
					'This field is mandatory, can be one of the following values: %s',
					ImportToolModule::formatList(self::types(), false)
				)
			],
			'SecurityIncident.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('Optiponal, describe the Security Incident in detail.'),
			],
			'SecurityIncident.Classification' => [
				'name' => __('Tags'),
				'model' => 'Classification',
				'callback' => [
					'beforeImport' => [$this, 'convertClassificationsImport']
				],
				'headerTooltip' => __('Optional, accepts multiple values separated by "|". For example "Critical|SOX|PCI".')
			],
			'SecurityIncident.open_date' => [
				'name' => __('Open Date'),
				'headerTooltip' => __('This field is mandatory, set the date when this incident was reported.'),
			],
			'SecurityIncident.auto_close_incident' => [
				'name' => __('Automatically Close Incident'),
				'headerTooltip' => __(
					'This field is mandatory, when all items on the lifecycle are completed this incident will change to "Closed" status automatically. Can be one of the following values: %s',
					ImportToolModule::formatList([0 => __('Disable auto close'), 1 => __('Enable auto close')], false)
				),
			],
			'SecurityIncident.closure_date' => [
				'name' => __('Closure Date'),
				'headerTooltip' => __('This field is mandatory only if "Status" is "Closed". If stages are defined and you set "Automatically Close" to "1" then the closure date will be updated once all states are completed, you can leave this empty.'),
			],
			'SecurityIncident.security_incident_status_id' => [
				'name' => __('Status'),
				'headerTooltip' => __(
					'This field is mandatory, can be one of the following values: %s',
					ImportToolModule::formatList(self::statuses(), false)
				),
			],
			'SecurityIncident.AssetRisk' => [
				'name' => __('Related Asset Risks'),
				'model' => 'AssetRisk',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a risk, you can find them at Risk Management / Asset Risk Management.'),
				'objectAutoFind' => true
			],
			'SecurityIncident.ThirdPartyRisk' => [
				'name' => __('Related Third Party Risks'),
				'model' => 'ThirdPartyRisk',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a third party risk, you can find them at Risk Management / Third Party Risk Management.'),
				'objectAutoFind' => true
			],
			'SecurityIncident.BusinessContinuity' => [
				'name' => __('Related Business Risks'),
				'model' => 'BusinessContinuity',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a business risk, you can find them at Risk Management / Business Impact Analysis.'),
				'objectAutoFind' => true
			],
			'SecurityIncident.Owner' => UserFields::getImportArgsFieldData('Owner', [
				'name' => $this->getFieldCollection()->get('Owner')->getLabel()
			]),
			'SecurityIncident.reporter' => [
				'name' => __('Reporter'),
				'headerTooltip' => __('Optional, is the individual that reported the incident.'),
			],
			'SecurityIncident.victim' => [
				'name' => __('Victim'),
				'headerTooltip' => __('Optional, is the individual that has been affected by the incident.'),
			],
			'SecurityIncident.SecurityService' => [
				'name' => __('Affected Compensating Controls'),
				'model' => 'SecurityService',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a control, you can find them at Controls Catalogue / Internal Controls.'),
				'objectAutoFind' => true
			],
			'SecurityIncident.Asset' => [
				'name' => __('Affected Asset'),
				'model' => 'Asset',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of an asset, you can find them at Asset Management / Asset Identification.'),
				'objectAutoFind' => true
			],
			'SecurityIncident.ThirdParty' => [
				'name' => __('Affected Third Parties'),
				'model' => 'ThirdParty',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a third party, you can find them at Organization / Third Parties.'),
				'objectAutoFind' => true
			],
		];
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications']['object_reminder'] = $this->_getModelObjectReminderNotification();
		
		$config['notifications'] = array_merge($config['notifications'], [
			'status_change' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.StatusChange',
				'key' => 'value',
				'label' => __('Status Change')
			]
		]);

		return $config;
	}

	public function getObjectStatusConfig() {
        return [
        	'closed' => [
            	'title' => __('Closed'),
            	'type' => 'success',
                'callback' => [$this, 'statusClosed'],
            ],
            'expired' => [// delete
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'hidden' => true,
                'regularTrigger' => true,
            ],
            'ongoing_incident' => [
            	'title' => __('Ongoing'),
                'callback' => [$this, '_statusOngoingIncident'],
                'trigger' => [
                    $this->Asset,
                    $this->SecurityService,
                    $this->AssetRisk,
                    $this->ThirdPartyRisk,
                    $this->BusinessContinuity,
                ]
            ],
            'lifecycle_incomplete' => [
            	'title' => __('Lifecycle Incomplete'),
                'callback' => [$this, '_statusLifecycleIncomplete'],
            ],
            'initiated' => [
                'trigger' => [
                    $this->SecurityIncidentStagesSecurityIncident
                ],
                'hidden' => true
            ],
            'completed' => [
                'trigger' => [
                    $this->SecurityIncidentStagesSecurityIncident
                ],
                'hidden' => true
            ],
        ];
    }

    public function statusClosed() {
        return (boolean) $this->find('count', [
            'conditions' => [
            	'SecurityIncident.id' => $this->id,
            	'SecurityIncident.security_incident_status_id' => self::STATUS_CLOSED
            ],
            'recursive' => -1
        ]);
    }

    public function statusOngoing() {
        return (boolean) $this->find('count', [
            'conditions' => [
            	'SecurityIncident.id' => $this->id,
            	'SecurityIncident.security_incident_status_id' => self::STATUS_ONGOING
            ],
            'recursive' => -1
        ]);
    }

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
            'SecurityIncident.closure_date < DATE(NOW())',
            'SecurityIncident.security_incident_status_id' => self::STATUS_ONGOING
        ]);
    }

    public function _statusOngoingIncident() {
    	$data = $this->find('count', [
    		'conditions' => [
    			'SecurityIncident.id' => $this->id,
    			'SecurityIncident.security_incident_status_id' => self::STATUS_ONGOING
    		],
    		'recursive' => -1
		]);

		return (boolean) $data;

    	// return (boolean) $this->statusOngoingIncident($this->id);
	}

	public function _statusLifecycleIncomplete() {
		$data = $this->SecurityIncidentStagesSecurityIncident->find('count', [
			'conditions' => [
				'SecurityIncidentStagesSecurityIncident.security_incident_id' => $this->id,
				'SecurityIncidentStagesSecurityIncident.status' => 0
			],
			'recursive' => -1
		]);

		return (boolean) $data && $this->statusOngoing();
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'Asset',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'SecurityService' => [
					'SecurityServiceAudit',
				],
				'ThirdParty',
			]
		];
	}

	public function getReportsConfig()
	{
		return [
			'finder' => [
                'options' => [
                    'contain' => [
                    	'SecurityIncidentStatus',
                    	'Classification',
                    	'SecurityIncidentStagesSecurityIncident' => [
                    		'SecurityIncidentStage',
                    		'SecurityIncident'
                    	],
                    	'SecurityService',
                    	'AssetRisk',
                    	'ThirdPartyRisk',
                    	'BusinessContinuity',
                    	'Asset',
                    	'ThirdParty',
                    	'SecurityIncidentStage',
                    	'Owner',
                    	'OwnerGroup',
                    	'CustomFieldValue'
                    ]
                ]
            ],
			'table' => [
				'model' => [
					'SecurityIncidentStagesSecurityIncident',
				]
			],
			'chart' => [
                1 => [
                    'title' => __('Incident Relationships'),
                    'description' => __('This chart shows what GRC elements are associated with this incident.'),
                    'type' => ReportBlockChartSetting::TYPE_TREE,
                    'templateType' => ReportTemplate::TYPE_ITEM,
                    'dataFn' => 'relatedObjectsChart'
                ],
            ]
		];
	}

	public function afterAuditProperty($Model, $propertyName, $oldValue, $newValue) {
		$statuses = $this->getStatuses();
		$this->propertyChangeNotification($propertyName, $oldValue, $newValue, 'security_incident_status_id', 'StatusChange', $statuses);
	}

	public function beforeValidate($options = array()) {
		$ret = true;

		$condAutoClose = $this->stagesExist() && !empty($this->data['SecurityIncident']['auto_close_incident']);
		$condClosed = !empty($this->data['SecurityIncident']['security_incident_status_id']) && $this->data['SecurityIncident']['security_incident_status_id'] != SECURITY_INCIDENT_CLOSED;

		if ($condAutoClose || $condClosed) {
			$this->validate['closure_date']['notEmpty']['required'] = false;
			$this->validate['closure_date']['notEmpty']['allowEmpty'] = true;
		}
		else {
			$this->validate['closure_date']['notEmpty']['required'] = true;
			$this->validate['closure_date']['notEmpty']['allowEmpty'] = false;
		}
		
		// $this->transformDataToHabtm(['SecurityService', 'AssetRisk', 'ThirdPartyRisk', 'BusinessContinuity',
		// 	'Asset', 'ThirdParty'
		// ]);

		// $this->setHabtmConditionsToData(['AssetRisk', 'ThirdPartyRisk', 'BusinessContinuity']);

		return $ret;
	}

	public function beforeSave($options = array())
	{
		if (isset($this->data[$this->alias]['security_incident_status_id'])
			&& $this->data[$this->alias]['security_incident_status_id'] == SECURITY_INCIDENT_CLOSED
			&& isset($this->data[$this->alias]['auto_close_incident'])
			&& !empty($this->data[$this->alias]['auto_close_incident'])
			&& empty($this->data[$this->alias]['closure_date'])
		) {
			$this->data[$this->alias]['closure_date'] = date('Y-m-d', time());
		}

		return true;
	}

	public function afterSave($created, $options = array()) {
		$ret = true;

		if (isset($this->data['SecurityIncident']['Classification'])) {
			$this->Classification->deleteAll(['Classification.security_incident_id' => $this->id]);
			$this->joinClassifications($this->data['SecurityIncident']['Classification'], $this->id);
		}

		return $ret;
	}

	public function joinClassifications($labels, $id) {
		if (empty($labels)) {
			return true;
		}

		foreach ($labels as $name) {
			$tmp = array(
				'security_incident_id' => $id,
				'name' => $name
			);

			$this->Classification->create();
			if (!$this->Classification->save($tmp)) {
				return false;
			}
		}

		return true;
	}

	public function getSecurityServices() {
		$data = $this->SecurityService->find('list', array(
			'order' => array('SecurityService.name' => 'ASC'),
			'fields' => array('SecurityService.id', 'SecurityService.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getAssets() {
		$data = $this->Asset->find('list', array(
			'order' => array('Asset.name' => 'ASC'),
			'fields' => array('Asset.id', 'Asset.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getThirdParties() {
		$data = $this->ThirdParty->find('list', array(
			'order' => array('ThirdParty.name' => 'ASC'),
			'fields' => array('ThirdParty.id', 'ThirdParty.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getBusinessRisks() {
		$data = $this->BusinessContinuity->find('list', array(
			'order' => array('BusinessContinuity.title' => 'ASC'),
			'fields' => array('BusinessContinuity.id', 'BusinessContinuity.title'),
			'recursive' => -1
		));
		return $data;
	}

	public function getThirdPartyRisks() {
		$data = $this->ThirdPartyRisk->find('list', array(
			'order' => array('ThirdPartyRisk.title' => 'ASC'),
			'fields' => array('ThirdPartyRisk.id', 'ThirdPartyRisk.title'),
			'recursive' => -1
		));
		return $data;
	}

	public function getAssetRisks() {
		$data = $this->AssetRisk->find('list', array(
			'order' => array('AssetRisk.title' => 'ASC'),
			'fields' => array('AssetRisk.id', 'AssetRisk.title'),
			'recursive' => -1
		));
		return $data;
	}

	public function getTypes() {
		return getSecurityIncidentTypes();
	}

	public function getClassifications() {
		$data = $this->Classification->find('list', array(
			'order' => array('Classification.name' => 'ASC'),
			'fields' => array('Classification.id', 'Classification.name'),
			'group' => array('Classification.name'),
			'recursive' => -1
		));
		$data = array_combine($data, $data);
		return $data;
	}

	public function getStatuses() {
		$data = $this->SecurityIncidentStatus->find('list', array(
			'order' => array('SecurityIncidentStatus.name' => 'ASC'),
			'fields' => array('SecurityIncidentStatus.id', 'SecurityIncidentStatus.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function findByClassifications($data) {
		$this->Classification->Behaviors->attach('Containable', array('autoFields' => false));
		$this->Classification->Behaviors->attach('Search.Searchable');

		$query = $this->Classification->getQuery('all', array(
			'conditions' => array(
				'Classification.id ' => $data['classification_id']
			),
			'contain' => array(),
			'fields' => array(
				'Classification.security_incident_id'
			),
		));

		return $query;
	}

	public function statusOngoingIncident($id) {
		$incidents = $this->SecurityIncidentsSecurityService->find('count', array(
			'conditions' => array(
				'SecurityIncidentsSecurityService.security_incident_id' => $id
			)
		));

		$incidents = $incidents || $this->SecurityIncidentsThirdParty->find('count', array(
			'conditions' => array(
				'SecurityIncidentsThirdParty.security_incident_id' => $id
			)
		));

		$incidents = $incidents || $this->RisksSecurityIncident->find('count', array(
			'conditions' => array(
				'RisksSecurityIncident.security_incident_id' => $id,
				'RisksSecurityIncident.risk_type' => 'asset-risk'
			)
		));

		$incidents = $incidents || $this->RisksSecurityIncident->find('count', array(
			'conditions' => array(
				'RisksSecurityIncident.security_incident_id' => $id,
				'RisksSecurityIncident.risk_type' => 'third-party-risk'
			)
		));

		$incidents = $incidents || $this->RisksSecurityIncident->find('count', array(
			'conditions' => array(
				'RisksSecurityIncident.security_incident_id' => $id,
				'RisksSecurityIncident.risk_type' => 'business-risk'
			)
		));

		$incidents = $incidents || $this->AssetsSecurityIncident->find('count', array(
			'conditions' => array(
				'AssetsSecurityIncident.security_incident_id' => $id
			)
		));

		if ($incidents) {
			return 1;
		}

		return 0;
	}

	public function statusLifecycleIncomplete($id) {
		$conditions = array(
			'SecurityIncidentStagesSecurityIncident.security_incident_id' => $id,
			'SecurityIncidentStagesSecurityIncident.status' => 0
		);
		$lifecycle = $this->SecurityIncidentStagesSecurityIncident->getItem($conditions);

		if(!empty($lifecycle)){
			//is incomplete
			return 1;
		}
		
		//is complete
		return 0;
	}

	public function lastIncompleteStep($id) {
		$stage = $this->SecurityIncidentStagesSecurityIncident->find('first', array(
			'conditions' => array(
				'SecurityIncidentStagesSecurityIncident.security_incident_id' => $id,
				'SecurityIncidentStagesSecurityIncident.status' => 0
			),
			'order' => array('SecurityIncidentStagesSecurityIncident.modified' => 'DESC'),
			'recursive' => 0
		));
// debug($stage);
		if (!empty($stage)) {
			return $stage['SecurityIncidentStage']['name'];
		}

		return false;
	}

	public function getSecurityIncidentTypes() {
		if (isset($this->data['SecurityIncident']['type'])) {
			return getSecurityIncidentTypes($this->data['SecurityIncident']['type']);
		}

		return false;
	}

	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'closure_date');
	}

	public function expiredStatusToQuery($expiredField = 'expired', $dateField = 'date') {
		if (!isset($this->data['SecurityIncident']['expired']) && isset($this->data['SecurityIncident']['closure_date'])) {
			$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
			if ($this->data['SecurityIncident']['closure_date'] < $today && $this->data['SecurityIncident']['security_incident_status_id'] == SECURITY_INCIDENT_ONGOING) {
				$this->data['SecurityIncident']['expired'] = '1';
			}
			else {
				$this->data['SecurityIncident']['expired'] = '0';
			}
		}
	}

	public function getSecurityIncidentStatuses() {
		if (isset($this->data['SecurityIncident']['security_incident_status_id'])) {
			$status = $this->SecurityIncidentStatus->find('first', array(
				'conditions' => array(
					'SecurityIncidentStatus.id' => $this->data['SecurityIncident']['security_incident_status_id']
				),
				'fields' => array('name'),
				'recursive' => -1
			));

			return $status['SecurityIncidentStatus']['name'];
		}

		return false;
	}

	public function logExpirations($ids) {
		$this->logToModel('SecurityService', $ids);
	}

	public function logToModel($model, $ids = array()) {
		$assocId = $this->hasAndBelongsToMany[$model]['associationForeignKey'];

		$habtmModel = $this->hasAndBelongsToMany[$model]['with'];

		$this->{$habtmModel}->bindModel(array(
			'belongsTo' => array('SecurityIncident')
		));

		//security_incident_id
		$foreignKey = $this->hasAndBelongsToMany[$model]['foreignKey'];
		$data = $this->{$habtmModel}->find('all', array(
			'conditions' => array(
				$habtmModel . '.' . $foreignKey => $ids
			),
			'fields' => array($habtmModel . '.' . $assocId, 'SecurityIncident.title'),
			'recursive' => 0
		));

		foreach ($data as $item) {
			$msg = __('Security Incident "%s" expired', $item['SecurityIncident']['title']);

			$this->{$model}->id = $item[$habtmModel][$assocId];
			$this->{$model}->addNoteToLog($msg);
			$this->{$model}->setSystemRecord($item[$habtmModel][$assocId], 2);
		}

	}

	public function getSecurityIncidentsList($conditions = array()){
		$data = $this->find('list', array(
			'conditions' => $conditions
		));
		return $data;
	}

	public function getSecurityIncident($conditions = array()){
		$data = $this->find('first', array(
			'conditions' => $conditions
		));
		return $data;
	}
	
	public function findByThirdParty($data = array()) {
		$this->SecurityIncidentsThirdParty->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->SecurityIncidentsThirdParty->Behaviors->attach('Search.Searchable');

		$query = $this->SecurityIncidentsThirdParty->getQuery('all', array(
			'conditions' => array(
				'SecurityIncidentsThirdParty.third_party_id' => $data['third_party_id']
			),
			'fields' => array(
				'SecurityIncidentsThirdParty.security_incident_id'
			)
		));

		return $query;
	}

	public function findByAsset($data = array()) {
		$this->AssetsSecurityIncident->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->AssetsSecurityIncident->Behaviors->attach('Search.Searchable');

		$query = $this->AssetsSecurityIncident->getQuery('all', array(
			'conditions' => array(
				'AssetsSecurityIncident.asset_id' => $data['asset_id']
			),
			'fields' => array(
				'AssetsSecurityIncident.security_incident_id'
			)
		));

		return $query;
	}

	public function convertClassificationsImport($value) {
		if (is_array($value)) {
			return $value;
		}

		return false;
	}

	public function getRiskWithProcedures($riskIds, $model) {
		return $this->{$model}->getProcedures($riskIds);
	}

	public function stagesExist() {
		return ((boolean) $this->SecurityIncidentStage->find('count'));
	}

	public function hasSectionIndex()
	{
		return true;
	}

}
