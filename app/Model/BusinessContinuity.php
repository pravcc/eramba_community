<?php
App::uses('BaseRisk', 'Model');
App::uses('RiskClassification', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('RiskAppetite', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');

class BusinessContinuity extends BaseRisk {
	public $displayField = 'title';
	public $scoreAssocModel = 'BusinessUnit';

	public $mapping = array(
		'titleColumn' => 'title',
		'logRecords' => true,
		'notificationSystem' => true,
        'workflow' => false,
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
        'AuditLog.Auditable' => array(
            'ignore' => array(
                'risk_score',
                'residual_risk',
                'created',
                'modified',
                'SecurityPolicy',
                'SecurityPolicyIncident',
                'SecurityIncident',
            )
        ),
        'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'impact', 'threats', 'vulnerabilities', 'residual_score', 'residual_risk', 'review', 'risk_mitigation_strategy_id',
                'description'
			)
		),
		'RiskManager',
		'RiskCalculationManager',
        'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
                'Reports.Report'
			]
		],
        'ObjectStatus.ObjectStatus',
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
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'BusinessUnit' => array(
			'rule' => array('multiple', array('min' => 1))
		),
		/*'Threat' => array(
			'rule' => array('multiple', array('min' => 1))
		),
		'Vulnerability' => array(
			'rule' => array('multiple', array('min' => 1))
		),*/
		'risk_mitigation_strategy_id' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		/*'BusinessContinuityPlan' => array(
			'rule' => array('multiple', array('min' => 1))
		),*/
		'residual_score' => array(
			'rule' => 'numeric',
			'required' => true
		),
		// 'RiskException' => array(
		// 	'rule' => array('multiple', array('min' => 1))
		// ),
		// 'review' => array(
		// 	'rule' => 'date',
		// 	'required' => true
		// )
	);

	public $belongsTo = array(
		'RiskMitigationStrategy'
	);

	public $hasMany = array(
		'BusinessContinuityReview' => array(
			'className' => 'BusinessContinuityReview',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'BusinessContinuityReview.model' => 'BusinessContinuity'
			)
		),
		'Review' => array(
			'className' => 'Review',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Review.model' => 'BusinessContinuity'
			)
		),
		'Tag' => array(
			'className' => 'Tag',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Tag.model' => 'BusinessContinuity'
			)
		)
	);
// 'Review', 'BusinessUnit', 'Process', 'SecurityService', 'RiskException', 'Project', 'SecurityPolicy', 'DataAsset', 'SecurityIncident'
	public $hasAndBelongsToMany = array(
		'BusinessUnit',
		'Process' => array(
			'with' => 'BusinessContinuitiesProcess',
			'foreignKey' => 'business_continuity_id'
		),
		'Threat',
		'Vulnerability',
		'SecurityService',
		'BusinessContinuityPlan',
		'RiskException',
		'RiskClassification' => [
			'className' => 'RiskClassification',
			'with' => 'BusinessContinuitiesRiskClassification',
			'joinTable' => 'business_continuities_risk_classifications',
			'foreignKey' => 'business_continuity_id',
			'associationForeignKey' => 'risk_classification_id',
			'conditions' => [
				'BusinessContinuitiesRiskClassification.type' => RiskClassification::TYPE_ANALYSIS
			]
		],
		'RiskClassificationTreatment' => [
			'className' => 'RiskClassification',
			'with' => 'BusinessContinuitiesRiskClassification',
			'joinTable' => 'business_continuities_risk_classifications',
			'foreignKey' => 'business_continuity_id',
			'associationForeignKey' => 'risk_classification_id',
			'conditions' => [
				'BusinessContinuitiesRiskClassification.type' => RiskClassification::TYPE_TREATMENT
			]
		],
		'ComplianceManagement',
		'Project' => array(
			'with' => 'BusinessContinuitiesProjects'
		),
		'SecurityPolicy' => [
			'className' => 'SecurityPolicy',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'security_policy_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'business-risk',
			)
		],
		'SecurityPolicyTreatment' => array(
			'className' => 'SecurityPolicy',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'security_policy_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'business-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_TREATMENT
			)
		),
		'SecurityPolicyIncident' => array(
			'className' => 'SecurityPolicy',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'security_policy_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'business-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_INCIDENT
			)
		),
		'SecurityIncident' => array(
			'with' => 'RisksSecurityIncident',
			'joinTable' => 'risks_security_incidents',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'security_incident_id',
			'conditions' => array(
				'RisksSecurityIncident.risk_type' => 'business-risk'
			)
		),
		'DataAsset' => array(
			'with' => 'DataAssetsRisk',
			'joinTable' => 'data_assets_risks',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'data_asset_id',
			'conditions' => array(
				'DataAssetsRisk.model' => 'BusinessContinuity'
			)
		),
		'RiskAppetiteThresholdAnalysis' => [
			'className' => 'RiskAppetiteThreshold',
			'with' => 'RiskAppetiteThresholdsRisk',
			'joinTable' => 'risk_appetite_thresholds_risks',
			'foreignKey' => 'foreign_key',
			'associationForeignKey' => 'risk_appetite_threshold_id',
			'conditions' => [
				'RiskAppetiteThresholdsRisk.type' => RiskClassification::TYPE_ANALYSIS,
				'RiskAppetiteThresholdsRisk.model' => 'BusinessContinuity',
			]
		],
		'RiskAppetiteThresholdTreatment' => [
			'className' => 'RiskAppetiteThreshold',
			'with' => 'RiskAppetiteThresholdsRisk',
			'joinTable' => 'risk_appetite_thresholds_risks',
			'foreignKey' => 'foreign_key',
			'associationForeignKey' => 'risk_appetite_threshold_id',
			'conditions' => [
				'RiskAppetiteThresholdsRisk.type' => RiskClassification::TYPE_TREATMENT,
				'RiskAppetiteThresholdsRisk.model' => 'BusinessContinuity',
			]
		],
	);

	public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Business Impact Analysis');
        $this->_group = parent::SECTION_GROUP_RISK_MGT;

        $this->fieldGroupData = array(
            'default' => array(
                'label' => __('General'),
                'order' => 1
            ),
            'analysis' => array(
                'label' => __('Analysis')
            ),
            'treatment' => array(
                'label' => __('Treatment')
            ),
            'incident-containment' => array(
                'label' => __('Incident Containment')
            ),
            'response-plan' => array(
                'label' => __('Risk Response Plan')
            ),
        );

        $this->fieldData = array(
            'BusinessUnit' => array(
                'label' => __('Applicable Business Units'),
                'description' => __('Select one or more business units (defined at Organisation / Business Units) that you want to include on the scope of this risk.'),
                'renderHelper' => ['BusinessContinuities', 'businessUnitField'],
                'group' => 'analysis',
                'editable' => true,
                'quickAdd' => true,
            ),
            'Process' => array(
                'label' => __('Processes'),
                'description' => __('Select one or more processes (defined under each business unit at Organisation / Bussiness Units) that you want to include on the scope of this Risk.'),
                'renderHelper' => ['BusinessContinuities', 'processField'],
                'group' => 'analysis',
                'editable' => true
            ),
            'impact' => array(
                'label' => __('Business Impact'),
                'description' => __('What is the Business Impact if this Risk materializes?'),
                'group' => 'analysis',
                'editable' => true
            ),
            'BusinessContinuityPlan' => array(
                'label' => __('Mitigating Business Continuity Plans'),
                'description' => __('Select which Business Continuity plans (defined at Control Catalogue / Business Continuity Plans) you wish to utilize to deal with the mitigation of this risk.'),
                'group' => 'treatment',
                'editable' => true,
                'quickAdd' => true,
            ),
            'plans_issues' => array(
                'label' => __('Plans Issues'),
                'type' => 'toggle',
                'hidden' => true
            ),
            'rpd' => array(
                'label' => __('Revenue per Day'),
                'description' => __('The total revenue per day is automatically calculated based on the processes chosen.'),
                'renderHelper' => ['BusinessContinuities', 'rpdField'],
                'editable' => false,
                'hidden' => true
            ),
            'rto' => array(
                'label' => __('Minimum RTO'),
                'description' => __('The minimum RTO objective is shown from is automatically calculated based on the processes chosen.'),
                'renderHelper' => ['BusinessContinuities', 'rtoField'],
                'editable' => false,
                'hidden' => true
            ),
            'mto' => array(
                'label' => __('Minimum MTO'),
                'description' => __('The minimum MTO objective is shown from is automatically calculated based on the processes chosen.'),
                'renderHelper' => ['BusinessContinuities', 'mtoField'],
                'editable' => false,
                'hidden' => true
            ),
        );

        //
        // Init helper Lib for UserFields Module
        $UserFields = new UserFields();
        //

		$this->notificationSystem = array(
			'macros' => array(
				'RISK_ID' => array(
					'field' => 'BusinessContinuity.id',
					'name' => __('Risk ID')
				),
				'RISK_NAME' => array(
					'field' => 'BusinessContinuity.title',
					'name' => __('Risk Name')
				),
				'RISK_OWNER' => $UserFields->getNotificationSystemData('Owner', [
					'name' => __('Risk Owner')
				]),
				'RISK_STAKEHOLDER' => $UserFields->getNotificationSystemData('Stakeholder', [
					'name' => __('Risk Stakeholder')
				]),
				'RISK_SCORE' => array(
					'field' => 'BusinessContinuity.risk_score',
					'name' => __('Risk Score')
				),
				'RISK_RESIDUAL' => array(
					'field' => 'BusinessContinuity.residual_risk',
					'name' => __('Risk Residual Score')
				),
				'RISK_STRATEGY' => array(
					'field' => 'RiskMitigationStrategy.name',
					'name' => __('Risk Mitigation Strategy')
				),
			),
			'customEmail' =>  true
		);

		parent::__construct($id, $table, $ds);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Business Risks'),
			'pdf_file_name' => __('business_risks'),
			'csv_file_name' => __('business_risks'),
            'additional_actions' => array(
                'BusinessContinuityReview' => array(
                    'label' => __('Reviews'),
                    'url' => array(
                        'controller' => 'reviews',
                        'action' => 'filterIndex',
                        'BusinessContinuityReview',
                        '?' => array(
                            'advanced_filter' => 1
                        )
                    )
                ),
            ),
            'history' => true,
            'bulk_actions' => true,
            'trash' => true,
            'view_item' => AppIndexCrudAction::VIEW_ITEM_QUERY,
            'use_new_filters' => true,
            'add' => true
		);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->_getAdvancedFilterConfig();

		$advancedFilterConfig
			->group('analysis')
				->multipleSelectField('BusinessUnit', [ClassRegistry::init('BusinessUnit'), 'getList'], [
					'label' => __('Business Unit'),
					'showDefault' => true,
					'insertOptions' => [
						'before' => 'Threat'
					]
				])
				->multipleSelectField('Process', [ClassRegistry::init('Process'), 'getList'], [
					'label' => __('Process'),
					'showDefault' => true,
					'insertOptions' => [
						'after' => 'BusinessUnit'
					]
				])
				->numberField('Process-rpd', [
					'label' => __('Process Revenue per Day'),
					'insertOptions' => [
						'after' => 'Process'
					]
				])
				->numberField('Process-rto', [
					'label' => __('Process RTO'),
					'showDefault' => true,
					'insertOptions' => [
						'after' => 'Process-rpd'
					]
				])
				->numberField('Process-rpo', [
					'label' => __('Process MTO'),
					'showDefault' => true,
					'insertOptions' => [
						'after' => 'Process-rto'
					]
				])
			->group('treatment')
				->multipleSelectField('BusinessContinuityPlan', [ClassRegistry::init('BusinessContinuityPlan'), 'getList'], [
					'label' => __('Business Continuity Plan'),
					'showDefault' => true,
					'insertOptions' => [
						'before' => 'RiskClassificationTreatment'
					]
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
			->group('BusinessContinuity', [
				'name' => __('Business Impact Analysis')
			])
				->multipleSelectField($this->alias, [$this, 'getList'], [
					'label' => __('Business Impact Analysis')
				]);

		return $advancedFilterConfig;
	}

	public function getObjectStatusConfig() {
        return parent::getObjectStatusConfig() + [
            'expired_reviews' => [
                'title' => __('Review Expired'),
                'callback' => [$this, '_statusExpiredReviews'],
                'trigger' => [
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.risks_with_missing_reviews'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.business_continuity_expired_reviews'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'risk_above_appetite' => [
                'title' => __('Above Appetite'),
                'callback' => [$this, '_statusRiskAboveAppetite'],
                'type' => 'danger',
                'trigger' => [
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.business_continuity_risk_above_appetite'
                    ],
                ]
            ],
        ];
    }

	public function statusExceptionsIssues() {
		$data = $this->RiskException->find('count', [
			'conditions' => [
				'RiskException.expired' => 1,
				'BusinessContinuitiesRiskException.business_continuity_id' => $this->id
			],
			'joins' => [
                [
                    'table' => 'business_continuities_risk_exceptions',
                    'alias' => 'BusinessContinuitiesRiskException',
                    'type' => 'INNER',
                    'conditions' => [
                        'BusinessContinuitiesRiskException.risk_exception_id = RiskException.id',
                    ]
                ],
            ],
			'recursive' => -1
		]);

		return (boolean) $data;
    }

    public function getNotificationSystemConfig()
    {
    	$config = parent::getNotificationSystemConfig();

    	return $config;
    }

    public function getReportsConfig()
    {
		return Hash::merge(parent::getReportsConfig(), [
			'finder' => [
				'options' => [
					'contain' => [
						'CustomFieldValue',
						'RiskMitigationStrategy',
						'BusinessContinuityReview',
						'Review' => [
							'User'
						],
						'Tag',
						'Process',
						'RiskClassification' => [
							'RiskClassificationType'
						],
						'RiskClassificationTreatment' => [
							'RiskClassificationType'
						],
						'Project',
						'SecurityPolicy',
						'SecurityPolicyTreatment',
						'SecurityPolicyIncident',
						'SecurityIncident',
						'DataAsset',
						'RiskAppetiteThresholdAnalysis',
						'RiskAppetiteThresholdTreatment',
						'BusinessUnit' => [
							'Process',
							'CustomFieldValue',
							'Asset',
							'BusinessContinuity',
							'Legal',
							'BusinessUnitOwner',
							'BusinessUnitOwnerGroup'
						],
						'Threat',
						'Vulnerability',
						'SecurityService',
						'BusinessContinuityPlan',
						'RiskException' => [
							'Tag',
							'CustomFieldValue',
							'Risk',
							'ThirdPartyRisk',
							'BusinessContinuity',
							'Requester',
							'RequesterGroup'
						],
						'ComplianceManagement',
						'Owner',
						'OwnerGroup',
						'Stakeholder',
						'StakeholderGroup'
					]
				]
			],
			'table' => [
				'model' => [
					'Review', 'RiskException',
				]
			]
		]);
	}

	public function getImportToolConfig()
	{
		$classificationType = $this->getFormClassifications();
		$classificationItems = $this->getAttachedClassifications();

		$classificationDescription = __('This field is mandatory, enter one classification from every type. Follow order of types.');

		foreach ($classificationType as $type) {
			$classificationDescription .= ' ' . __('Type') . ' ' . $type['RiskClassificationType']['name'] . ': ' . ImportToolModule::formatList($classificationItems[$type['RiskClassificationType']['id']], false) . ';';
		}

		$residualScore = [];

		if ($this->getRiskAppetiteType() != RiskAppetite::TYPE_THRESHOLD) {
			$residualScore = [
				'BusinessContinuity.residual_score' => [
					'name' => __('Residual Score'),
					'headerTooltip' => __(
						'This field is mandatory, enter the percentage of Risk Reduction that was achieved by applying Security Controls. Can be one of the following values: %s',
						ImportToolModule::formatList(getReversePercentageOptions(), false)
					),
				],
			];
		}

		return [
			'BusinessContinuity.title' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory, give this risk a descriptive title.'),
			],
			'BusinessContinuity.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('Optional, describe this risk scenario, context, triggers, Etc.'),
			],
			'BusinessContinuity.Owner' => UserFields::getImportArgsFieldData('Owner', [
				'name' => $this->getFieldCollection()->get('Owner')->getLabel()
			]),
			'BusinessContinuity.Stakeholder' => UserFields::getImportArgsFieldData('Stakeholder', [
				'name' => $this->getFieldCollection()->get('Stakeholder')->getLabel()
			]),
			'BusinessContinuity.Tag' => [
				'name' => __('Tags'),
				'model' => 'Tag',
				'callback' => [
					'beforeImport' => [$this, 'convertTagsImport']
				],
				'headerTooltip' => __('Optional, accepts tags separated by "|". For example "Critical|High Risk|Financial Risk".')
			],
			'BusinessContinuity.review' => [
				'name' => __('Review'),
				'headerTooltip' => __('This field is mandatory, define a date when this risk will be reviewed, the format for the date is YYYY-MM-DD and the date must be in the future.'),
			],
			'BusinessContinuity.BusinessUnit' => [
				'name' => __('Applicable Business Units'),
				'model' => 'BusinessUnit',
				'headerTooltip' => __('This field is mandatory, accepts multiple names separated by "|". You need to enter the name of a business unit, you can find them at Organization / Business Units.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.Process' => [
				'name' => __('Processes'),
				'model' => 'Process',
				'headerTooltip' => __('Optiopnal, accepts multiple names separated by "|". You need to enter the name of a process, you can find them at Organization / Business Units / Processes.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.impact' => [
				'name' => __('What is the Business Impact if this Risk materializes?'),
				'headerTooltip' => __('Optional.'),
			],
			'BusinessContinuity.Threat' => [
				'name' => __('Threat Tags'),
				'model' => 'Threat',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a threat, you can find them at Risk Management / Asset Risk Management / Settings / Threats.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.threats' => [
				'name' => __('Threat Description'),
				'headerTooltip' => __('Optional, describe the context of the threats vectors for this risk.'),
			],
			'BusinessContinuity.Vulnerability' => [
				'name' => __('Vulnerabilities Tags'),
				'model' => 'Vulnerability',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a vulnerability, you can find them at Risk Management / Asset Risk Management / Settings / Vulnerabilities.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.vulnerabilities' => [
				'name' => __('Vulnerabilities Description'),
				'headerTooltip' => __('Optional, describe the context of the vulnerabilities vectors for this risk.'),
			],
			'BusinessContinuity.RiskClassification' => [
				'name' => __('Risk Anaylsis Classification'),
				'model' => 'RiskClassification',
				'headerTooltip' => $classificationDescription,
			],
			'BusinessContinuity.BusinessContinuityPlan' => [
				'name' => __('Mitigating Business Continuity Plans'),
				'model' => 'BusinessContinuityPlan',
				'headerTooltip' => __('Optiopnal, accepts multiple names separated by "|". You need to enter the name of a plan, you can find them at Controls Catalogue / Business Continuity Plans.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.risk_mitigation_strategy_id' => [
				'name' => __('Risk Treatment'),
				'headerTooltip' => __(
					'This field is mandatory, select id of treatment strategy for this risk, can be one of the following values: %s',
					ImportToolModule::formatList(self::mitigationStrategies(), false)
				),
			],
			'BusinessContinuity.SecurityService' => [
				'name' => __('Treatment: Internal Controls'),
				'model' => 'SecurityService',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of a control, you can find them at Controls Catalogue / Internal Controls.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.SecurityPolicyTreatment' => [
				'name' => __('Treatment: Security Policies'),
				'model' => 'SecurityPolicyTreatment',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of a policy, you can find them at Controls Catalogue / Security Policies.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.RiskException' => [
				'name' => __('Treatment: Risk Exceptions'),
				'model' => 'RiskException',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of an exception, you can find them at Risk Management / Risk Exceptions.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.Project' => [
				'name' => __('Treatment: Projects'),
				'model' => 'Project',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of a project, you can find them at Security Operations / Project Management.'),
				'objectAutoFind' => true
			],
			'BusinessContinuity.RiskClassificationTreatment' => [
				'name' => __('Risk Treatment Classification'),
				'model' => 'RiskClassificationTreatment',
				'headerTooltip' => $classificationDescription,
			],
			'BusinessContinuity.SecurityPolicyIncident' => [
				'name' => __('Risk Response Documents'),
				'model' => 'SecurityPolicyIncident',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a document, you can find them at Controls Catalogue / Security Policies.'),
				'objectAutoFind' => true
			],
		] + $residualScore;
	}

    public function getSectionInfoConfig()
    {
        return [
            'map' => [
                'BusinessUnit' => [
                    'Process',
                ],
                'SecurityService' => [
                    'SecurityServiceAudit',
                    'SecurityServiceIssue',
                    'SecurityServiceMaintenance',
                ],
                'BusinessContinuityPlan' => [
                    'BusinessContinuityPlanAudit',
                ],
                'Project' => [
                    'ProjectAchievement',
                ],
                'SecurityPolicy',
                'RiskException'
            ]
        ];
    }

	public function beforeSave($options = array()) {
		$ret = true;

		if (isset($this->data['BusinessContinuity']['RiskClassificationTreatment']) && isset($this->data['BusinessContinuity']['Asset'])) {
			if ($this->getMethod() != 'magerit') {
				$this->data['BusinessContinuity']['RiskClassificationTreatment'] = array_filter($this->data['BusinessContinuity']['RiskClassificationTreatment']);
			}
		}

		$ret &= parent::beforeSave($options);

        // $this->transformDataToHabtm(array('BusinessUnit', 'Asset', 'Threat', 'Vulnerability', 'SecurityService', 
        //     'RiskException', 'Project', 'Process', 'SecurityPolicyTreatment', 'SecurityPolicyIncident', 'RiskClassification', 'RiskClassificationTreatment', 'BusinessContinuityPlan'
        // ));

        // $this->setHabtmConditionsToData(array('SecurityPolicyTreatment', 'SecurityPolicyIncident'));

		if (isset($this->data['BusinessContinuity']['risk_score']) && is_numeric($this->data['BusinessContinuity']['risk_score'])) {
			$this->data['BusinessContinuity']['risk_score'] = CakeNumber::precision($this->data['BusinessContinuity']['risk_score'], 2);
		}

		return $ret;
	}

	public function afterSave($created, $options = array()) {
        parent::afterSave($created, $options);

        if (!empty($this->id) && $this->exists()) {
        	$this->saveRiskScoreWrapper($this->id);
        }
	}

	/**
	 * Get relevant classification for this section.
	 */
	public function getFormClassifications()
	{
		return $this->getDefaultFormClassifications();
	}

	public function findByBusinessUnit($data = array()) {
        $this->BusinessContinuitiesBusinessUnit->Behaviors->attach('Containable', array(
                'autoFields' => false
            )
        );
        $this->BusinessContinuitiesBusinessUnit->Behaviors->attach('Search.Searchable');

        $query = $this->BusinessContinuitiesBusinessUnit->getQuery('all', array(
            'conditions' => array(
                'BusinessContinuitiesBusinessUnit.business_unit_id' => $data['bu_id']
            ),
            'fields' => array(
                'BusinessContinuitiesBusinessUnit.business_continuity_id'
            )
        ));

        return $query;
    }

	public function getControlsWithIssues($riskId, $find = 'list') {
		$this->BusinessContinuitiesSecurityService->bindModel(array(
			'belongsTo' => array('SecurityService')
		));

		$ids = $this->BusinessContinuitiesSecurityService->find('list', array(
			'conditions' => array(
				'BusinessContinuitiesSecurityService.business_continuity_id' => $riskId
			),
			'fields' => array('SecurityService.id'),
			'recursive' => 0
		));

		$issues = $this->SecurityService->getIssues($ids, $find);

		return $issues;
	}

	public function controlsIssuesMsgParams() {
		if (isset($this->data['BusinessContinuity']['id'])) {
			$issues = $this->getControlsWithIssues($this->data['BusinessContinuity']['id']);
		}
		elseif (isset($this->id)) {
			$issues = $this->getControlsWithIssues($this->id);
		}

		if (!empty($issues)) {
			return implode(', ', $issues);
		}
	}

    /**
     * @deprecated
     */
	public function saveExceptionIssues($riskId) {
		if (is_array($riskId)) {
			$ret = true;
			foreach ($riskId as $id) {
				$ret &= $this->saveExceptionIssues($id);
			}

			return $ret;
		}

		$issues = $this->getExceptionWithIssues($riskId);

		if (empty($issues)) {
			$hasIssues = '0';
		}
		else {
			$hasIssues = '1';
		}

		$saveData = array('exceptions_issues' => (string) (int) $hasIssues);

		$this->id = $riskId;
		return (bool) $this->save($saveData, array('validate' => false, 'callbacks' => 'before'));
	}

	public function getExceptionWithIssues($riskId, $find = 'list') {
		$this->BusinessContinuitiesRiskException->bindModel(array(
			'belongsTo' => array('RiskException')
		));

		$issues = $this->BusinessContinuitiesRiskException->find($find, array(
			'conditions' => array(
				'BusinessContinuitiesRiskException.business_continuity_id' => $riskId,
				'RiskException.expired' => 1
			),
			'fields' => array('RiskException.id', 'RiskException.title'),
			'recursive' => 0
		));

		return $issues;
	}

	public function exceptionsIssuesMsgParams() {

		if (isset($this->data['BusinessContinuity']['id'])) {
			$issues = $this->getExceptionWithIssues($this->data['BusinessContinuity']['id']);
		}
		elseif (isset($this->id)) {
			$issues = $this->getExceptionWithIssues($this->id);
		}

		if (!empty($issues)) {
			return implode(', ', $issues);
		}
	}

	public function queryControlsIssues() {
		if ($this->id != null && !isset($this->data['BusinessContinuity']['controls_issues'])) {

			if (!isset($this->data['BusinessContinuity']['business_continuity_plan_id'])) {
				$this->BusinessContinuitiesBusinessContinuityPlan->bindModel(array(
					'belongsTo' => array('BusinessContinuityPlan')
				));

				$ids = $this->BusinessContinuitiesBusinessContinuityPlan->find('list', array(
					'conditions' => array(
						'BusinessContinuitiesBusinessContinuityPlan.business_continuity_id' => $this->id
					),
					'fields' => array('BusinessContinuityPlan.id'),
					'recursive' => 0
				));

				$this->data['BusinessContinuity']['business_continuity_plan_id'] = $ids;
			}

			$data = $this->BusinessContinuityPlan->find('list', array(
				'conditions' => array(
					'OR' => array(
						array(
							'BusinessContinuityPlan.id' => $this->data['BusinessContinuity']['business_continuity_plan_id'],
							'BusinessContinuityPlan.audits_all_done' => 0
						),
						array(
							'BusinessContinuityPlan.id' => $this->data['BusinessContinuity']['business_continuity_plan_id'],
							'BusinessContinuityPlan.audits_last_passed' => 0
						)
					)
				),
				'fields' => array('BusinessContinuityPlan.id', 'BusinessContinuityPlan.title'),
				'recursive' => 0
			));

			if (!empty($data)) {
				$this->data['BusinessContinuity']['controls_issues'] = '1';

				return array(implode(', ', $data));
			}
			else {
				$this->data['BusinessContinuity']['controls_issues'] = '0';
			}
		}

		return null;
	}

	/**
	 * Append expired field to the query calculated from review date field.
	 */
	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'review');
	}

	/**
	 * Calculate Risk Score for a Risk from given classification values.
	 * @return int Risk Score.
	 */
	public function calculateRiskScore($classification_ids = array(), $bu_ids = array()) {
		$this->resetCalculationClass();
		
		return $this->calculateByMethod(array(
			'classification_ids' => $classification_ids,
			'bu_ids' => $bu_ids,
		));
	}

	/**
	 * Calculate Business Continuity Risk Score from given classification values.
	 * @return int Risk Score.
	 */
	public function ErambaCalculation($classification_ids = array(), $bu_ids = array()) {
		//$classification_ids = !empty($classification_ids) ? $classification_ids : $this->request->data['BusinessContinuity']['risk_classification_id'];
		if ( empty( $classification_ids ) ) {
			return 0;
		}

		$classifications = $this->RiskClassification->find('all', array(
			'conditions' => array(
				'RiskClassification.id' => $classification_ids
			),
			'fields' => array( 'id', 'value' ),
			'recursive' => -1
		));

		//$bu_ids = !empty($bu_ids) ? $bu_ids : $this->request->data['BusinessContinuity']['business_unit_id'];
		$business_units = $this->BusinessUnit->find('all', array(
			'conditions' => array(
				'BusinessUnit.id' => $bu_ids
			),
			'fields' => array( 'id' ),
			'contain' => array(
				'Legal' => array(
					'fields' => array( 'id', 'risk_magnifier' )
				)
			),
			'recursive' => 0
		));

		return array($classifications, $business_units);

		/*$classification_sum = 0;
		foreach ( $classifications as $classification ) {
			$classification_sum += $classification['RiskClassification']['value'];
		}

		$bu_sum = 0;
		foreach ( $business_units as $bu ) {
			foreach ($bu['Legal'] as $legal) {
				$bu_sum += $legal['risk_magnifier'];
			}
		}

		if ( $bu_sum ) {
			return $classification_sum * $bu_sum;
		}

		return $classification_sum;*/
	}

	/**
	 * Calculate risk score and save to database.
	 */
	public function calculateAndSaveRiskScoreById($Ids) {
		$bc = $this->find('all', array(
			'conditions' => array(
				'BusinessContinuity.id' => $Ids
			),
			'contain' => array(
				'BusinessUnit' => array(
					'fields' => array('id')
				),
				'RiskClassification' => array(
					'fields' => array('id')
				)
			)
		));

		$ret = true;
		foreach ($bc as $risk) {
			$classificationIds = array();
			foreach ($risk['RiskClassification'] as $c) {
				$classificationIds[] = $c['id'];
			}

			$buIds = array();
			foreach ($risk['BusinessUnit'] as $b) {
				$buIds[] = $b['id'];
			}

			$riskScore = $this->calculateRiskScore($classificationIds, $buIds);
			if (!is_numeric($riskScore)) {
				return false;
			}
			$residualRisk = getResidualRisk($risk['BusinessContinuity']['residual_score'], $riskScore);

			$saveData = array(
				'risk_score' => $riskScore,
				'residual_risk' => $residualRisk
			);

			$this->id = $risk['BusinessContinuity']['id'];
			$oldRiskScore = $this->field('risk_score');

			$ret &= (bool) $this->save($saveData, false);

			if (!empty($this->logAfterRiskScoreChange)) {
				$msg = $this->logAfterRiskScoreChange['message'];
				$args = $this->logAfterRiskScoreChange['args'];
				$args[] = $oldRiskScore;
				$args[] = $riskScore;

				array_unshift($args, $msg);
				$msg = call_user_func_array('sprintf', $args);
				$ret &= $this->quickLogSave($risk['BusinessContinuity']['id'], 2, $msg);
			}
		}

		return $ret;
	}

	public function controlsIssueConditions($data = array()){
		$conditions = array();
		if($data['controls_issues'] == 1){
			$conditions = array(
				'BusinessContinuity.controls_issues >' => 0
			);
		}
		elseif($data['controls_issues'] == 0){
			$conditions = array(
				'BusinessContinuity.controls_issues' => 0
			);
		}

		return $conditions;
	}

	public function residualRiskConditions($data = array()){
		$conditions = array();
		if($data['residual_risk'] == 1){
			$conditions = array(
				'BusinessContinuity.residual_risk >' => RISK_APPETITE
			);
		}
		elseif($data['residual_risk'] == 0){
			$conditions = array(
				'BusinessContinuity.residual_risk <=' => RISK_APPETITE
			);
		}

		return $conditions;
	}

	public function getBusinessUnits() {
		$data = $this->BusinessUnit->find('list', array(
            'order' => array('BusinessUnit.name' => 'ASC'),
            'fields' => array('BusinessUnit.id', 'BusinessUnit.name'),
            'recursive' => -1
        ));
        return $data;
	}

	public function getProcesses() {
		$data = $this->Process->find('list', array(
            'order' => array('Process.name' => 'ASC'),
            'fields' => array('Process.id', 'Process.name'),
            'recursive' => -1
        ));
        return $data;
	}

	public function getBusinessContinuityPlans() {
		$data = $this->BusinessContinuityPlan->find('list', array(
            'order' => array('BusinessContinuityPlan.title' => 'ASC'),
            'fields' => array('BusinessContinuityPlan.id', 'BusinessContinuityPlan.title'),
            'recursive' => -1
        ));
        return $data;
	}
}
