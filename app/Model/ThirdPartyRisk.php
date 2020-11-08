<?php
App::uses('BaseRisk', 'Model');
App::uses('RiskClassification', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('RiskAppetite', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');

class ThirdPartyRisk extends BaseRisk {
	public $displayField = 'title';
	public $scoreAssocModel = 'ThirdParty';

	public $mapping = array(
		'titleColumn' => 'title',
		'notificationSystem' => array('index'),
		'logRecords' => true,
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
				'title', 'shared_information', 'controlled', 'threats', 'vulnerabilities', 'residual_score', 'risk_score', 'residual_risk', 'review', 'risk_mitigation_strategy_id',
                'description'
			)
		),
		'RiskManager',
		'RiskCalculationManager',
        'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
                'Reports.Report',
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
		'ThirdParty' => array(
			'rule' => array( 'multiple', array( 'min' => 1 ) )
		),
		'Asset' => array(
			'rule' => array( 'multiple', array( 'min' => 1 ) )
		),
		/*'Threat' => array(
			'rule' => array( 'multiple', array( 'min' => 1 ) )
		),
		'Vulnerability' => array(
			'rule' => array( 'multiple', array( 'min' => 1 ) )
		),*/
		'risk_mitigation_strategy_id' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		// 'SecurityService' => array(
		// 	'rule' => array( 'multiple', array( 'min' => 1 ) )
		// ),
		'residual_score' => array(
			'rule' => 'numeric',
			'required' => true
		),
		// 'RiskException' => array(
		// 	'rule' => array( 'multiple', array( 'min' => 1 ) )
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
		'ThirdPartyRiskReview' => array(
			'className' => 'ThirdPartyRiskReview',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'ThirdPartyRiskReview.model' => 'ThirdPartyRisk'
			)
		),
		'Review' => array(
			'className' => 'Review',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Review.model' => 'ThirdPartyRisk'
			)
		),
		'Tag' => array(
			'className' => 'Tag',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Tag.model' => 'ThirdPartyRisk'
			)
		)
	);

	public $hasAndBelongsToMany = array(
		'ThirdParty',
		'Asset',
		'Threat',
		'Vulnerability',
		'SecurityService',
		'RiskException',
		'RiskClassification' => [
			'className' => 'RiskClassification',
			'with' => 'RiskClassificationsThirdPartyRisk',
			'joinTable' => 'risk_classifications_third_party_risks',
			'foreignKey' => 'third_party_risk_id',
			'associationForeignKey' => 'risk_classification_id',
			'conditions' => [
				'RiskClassificationsThirdPartyRisk.type' => RiskClassification::TYPE_ANALYSIS
			]
		],
		'RiskClassificationTreatment' => [
			'className' => 'RiskClassification',
			'with' => 'RiskClassificationsThirdPartyRisk',
			'joinTable' => 'risk_classifications_third_party_risks',
			'foreignKey' => 'third_party_risk_id',
			'associationForeignKey' => 'risk_classification_id',
			'conditions' => [
				'RiskClassificationsThirdPartyRisk.type' => RiskClassification::TYPE_TREATMENT
			]
		],
		'ComplianceManagement',
		'Project' => array(
			'with' => 'ProjectsThirdPartyRisk'
		),
		'SecurityPolicy' => [
			'className' => 'SecurityPolicy',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'security_policy_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'third-party-risk',
			)
		],
		'SecurityPolicyTreatment' => array(
			'className' => 'SecurityPolicy',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'security_policy_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'third-party-risk',
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
				'RisksSecurityPolicy.risk_type' => 'third-party-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_INCIDENT
			)
		),
		'SecurityIncident' => array(
			'with' => 'RisksSecurityIncident',
			'joinTable' => 'risks_security_incidents',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'security_incident_id',
			'conditions' => array(
				'RisksSecurityIncident.risk_type' => 'third-party-risk'
			)
		),
		'DataAsset' => array(
			'with' => 'DataAssetsRisk',
			'joinTable' => 'data_assets_risks',
			'foreignKey' => 'risk_id',
			'associationForeignKey' => 'data_asset_id',
			'conditions' => array(
				'DataAssetsRisk.model' => 'ThirdPartyRisk'
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
				'RiskAppetiteThresholdsRisk.model' => 'ThirdPartyRisk',
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
				'RiskAppetiteThresholdsRisk.model' => 'ThirdPartyRisk',
			]
		],
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
        $this->label = __('Third Party Risk Management');
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
            'ThirdParty' => array(
                'label' => __('Applicable Third Parties'),
                'renderHelper' => ['ThirdPartyRisks', 'thirdPartyField'],
                'group' => 'analysis',
                'editable' => true,
                'quickAdd' => true,
            ),
            'Asset' => array(
                'label' => __('Shared Assets with this Third Party'),
                'renderHelper' => ['ThirdPartyRisks', 'assetField'],
                'group' => 'analysis',
                'editable' => true,
                'quickAdd' => true,
            ),
            'shared_information' => array(
                'label' => __('Why is Information shared with these Third Parties'),
                'group' => 'analysis',
                'editable' => true
            ),
            'controlled' => array(
                'label' => __('How it will be Controlled?'),
                'group' => 'analysis',
                'editable' => true
            ),
        );

		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//

		$this->notificationSystem = array(
			'macros' => array(
				'RISK_ID' => array(
					'field' => 'ThirdPartyRisk.id',
					'name' => __('Risk ID')
				),
				'RISK_NAME' => array(
					'field' => 'ThirdPartyRisk.title',
					'name' => __('Risk Name')
				),
				'RISK_OWNER' => $UserFields->getNotificationSystemData('Owner', [
					'name' => __('Risk Owner')
				]),
				'RISK_STAKEHOLDER' => $UserFields->getNotificationSystemData('Stakeholder', [
					'name' => __('Risk Stakeholder')
				]),
				'RISK_SCORE' => array(
					'field' => 'ThirdPartyRisk.risk_score',
					'name' => __('Risk Score')
				),
				'RISK_RESIDUAL' => array(
					'field' => 'ThirdPartyRisk.residual_risk',
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
			'pdf_title' => __('Third Party Risks'),
			'pdf_file_name' => __('third_party_risks'),
			'csv_file_name' => __('third_party_risks'),
            'additional_actions' => array(
                'ThirdPartyRiskReview' => array(
                    'label' => __('Reviews'),
                    'url' => array(
                        'controller' => 'reviews',
                        'action' => 'filterIndex',
                        'ThirdPartyRiskReview',
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
				->multipleSelectField('ThirdParty', [ClassRegistry::init('ThirdParty'), 'getList'], [
					'label' => __('Third Party'),
					'showDefault' => true,
					'insertOptions' => [
						'before' => 'Threat'
					]
				])
				->multipleSelectField('Asset', [ClassRegistry::init('Asset'), 'getList'], [
					'label' => __('Asset'),
                    'showDefault' => true,
                    'insertOptions' => [
                    	'after' => 'ThirdParty'
                    ]
                ]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
			->group('ThirdPartyRisk', [
				'name' => __('Third Party Risk')
			])
				->multipleSelectField($this->alias, [$this, 'getList'], [
					'label' => __('Third Party Risk')
				]);

		return $advancedFilterConfig;
	}

	public function getNotificationSystemConfig()
    {
    	$config = parent::getNotificationSystemConfig();

    	return $config;
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
                        'trigger' => 'ObjectStatus.trigger.third_party_risk_expired_reviews'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'risk_above_appetite' => [
                'title' => __('Above Appetite'),
                'type' => 'danger',
                'callback' => [$this, '_statusRiskAboveAppetite'],
                'trigger' => [
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.third_party_risk_risk_above_appetite'
                    ],
                ]
            ],
        ];
    }

	public function statusExceptionsIssues() {
		$data = $this->RiskException->find('count', [
			'conditions' => [
				'RiskException.expired' => 1,
				'RiskExceptionsThirdPartyRisk.third_party_risk_id' => $this->id
			],
			'joins' => [
                [
                    'table' => 'risk_exceptions_third_party_risks',
                    'alias' => 'RiskExceptionsThirdPartyRisk',
                    'type' => 'INNER',
                    'conditions' => [
                        'RiskExceptionsThirdPartyRisk.risk_exception_id = RiskException.id',
                    ]
                ],
            ],
			'recursive' => -1
		]);

		return (boolean) $data;
    }

    public function getReportsConfig()
    {
		return Hash::merge(parent::getReportsConfig(), [
			'finder' => [
				'options' => [
					'contain' => [
						'RiskMitigationStrategy',
						'ThirdPartyRiskReview',
						'Review' => [
							'User'
						],
						'Tag',
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
						'ThirdParty' => [
							'ThirdPartyType',
							'ServiceContract',
							'ComplianceAudit',
							'CustomFieldValue',
							'ThirdPartyRisk',
							'Legal',
							'SecurityIncident',
							'Sponsor',
							'SponsorGroup'
						],
						'Asset' => [
							'BusinessUnit'
						],
						'Threat',
						'Vulnerability',
						'SecurityService',
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
						'StakeholderGroup',
						'CustomFieldValue'
					]
				]
			],
			'table' => [
				'model' => [
					'Review', 'ThirdParty', 'RiskException'
				]
			],
			'chart' => [
				1 => [
					'title' => __('Risks by Business Unit'),
					'description' => __('We show the relationship in between Risks and Business Units (trough the assets they have in common).'),
					'type' => ReportBlockChartSetting::TYPE_DOUGHNUT,
					'templateType' => ReportTemplate::TYPE_SECTION,
					'dataFn' => 'businessUnitsChart'
				],
				2 => [
					'title' => __('Risks by Third Party'),
					'description' => __('We show the relationship in between Risks and Third Parties.'),
					'type' => ReportBlockChartSetting::TYPE_DOUGHNUT,
					'templateType' => ReportTemplate::TYPE_SECTION,
					'dataFn' => 'thirdPartiesChart'
				],
				3 => [
					'title' => __('Risks and related Objects'),
					'description' => __('This tree shows the risks and its asociated assets, third parties, vulnerabilities, threats, controls, policies and exceptions.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'relatedObjectsChart'
				],
			]
		]);
	}

    public function getSectionInfoConfig()
    {
        return [
            'map' => [
                'Asset' => [
                    'BusinessUnit',
                ],
                'ThirdParty',
                'SecurityService' => [
                    'SecurityServiceAudit',
                    'SecurityServiceIssue',
                    'SecurityServiceMaintenance',
                ],
                'Project' => [
                    'ProjectAchievement',
                ],
                'SecurityPolicy',
                'RiskException'
            ]
        ];
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
				'ThirdPartyRisk.residual_score' => [
					'name' => __('Residual Score'),
					'headerTooltip' => __(
						'This field is mandatory, enter the percentage of Risk Reduction that was achieved by applying Security Controls. Can be one of the following values: %s',
						ImportToolModule::formatList(getReversePercentageOptions(), false)
					),
				],
			];
		}

		return [
			'ThirdPartyRisk.title' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory, give this risk a descriptive title.'),
			],
			'ThirdPartyRisk.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('Optional, describe this risk scenario, context, triggers, Etc.'),
			],
			'ThirdPartyRisk.Owner' => UserFields::getImportArgsFieldData('Owner', [
				'name' => $this->getFieldCollection()->get('Owner')->getLabel()
			]),
			'ThirdPartyRisk.Stakeholder' => UserFields::getImportArgsFieldData('Stakeholder', [
				'name' => $this->getFieldCollection()->get('Stakeholder')->getLabel()
			]),
			'ThirdPartyRisk.Tag' => [
				'name' => __('Tags'),
				'model' => 'Tag',
				'callback' => [
					'beforeImport' => [$this, 'convertTagsImport']
				],
				'headerTooltip' => __('Optional, accepts tags separated by "|". For example "Critical|High Risk|Financial Risk".')
			],
			'ThirdPartyRisk.review' => [
				'name' => __('Review'),
				'headerTooltip' => __('This field is mandatory, define a date when this risk will be reviewed, the format for the date is YYYY-MM-DD and the date must be in the future.'),
			],
			'ThirdPartyRisk.ThirdParty' => [
				'name' => __('Applicable Third Parties'),
				'model' => 'ThirdParty',
				'headerTooltip' => __('This field is mandatory, accepts multiple names separated by "|". You need to enter the name of a third party, you can find them at Organization / Third Parties.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.Asset' => [
				'name' => __('Shared Assets with this Third Party'),
				'model' => 'Asset',
				'headerTooltip' => __('This field is mandatory, accepts multiple names separated by "|". You need to enter the name of an asset, you can find them at Asset Management / Asset Identification.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.shared_information' => [
				'name' => __('Why is Information shared with these Third Parties'),
				'headerTooltip' => __('Optional.'),
			],
			'ThirdPartyRisk.controlled' => [
				'name' => __('How it will be Controlled?'),
				'headerTooltip' => __('Optional.'),
			],
			'ThirdPartyRisk.Threat' => [
				'name' => __('Threat Tags'),
				'model' => 'Threat',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a threat, you can find them at Risk Management / Asset Risk Management / Settings / Threats.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.threats' => [
				'name' => __('Threat Description'),
				'headerTooltip' => __('Optional, describe the context of the threats vectors for this risk.'),
			],
			'ThirdPartyRisk.Vulnerability' => [
				'name' => __('Vulnerabilities Tags'),
				'model' => 'Vulnerability',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a vulnerability, you can find them at Risk Management / Asset Risk Management / Settings / Vulnerabilities.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.vulnerabilities' => [
				'name' => __('Vulnerabilities Description'),
				'headerTooltip' => __('Optional, describe the context of the vulnerabilities vectors for this risk.'),
			],
			'ThirdPartyRisk.RiskClassification' => [
				'name' => __('Risk Anaylsis Classification'),
				'model' => 'RiskClassification',
				'headerTooltip' => $classificationDescription,
			],
			'ThirdPartyRisk.risk_mitigation_strategy_id' => [
				'name' => __('Risk Treatment'),
				'headerTooltip' => __(
					'This field is mandatory, select id of treatment strategy for this risk, can be one of the following values: %s',
					ImportToolModule::formatList(self::mitigationStrategies(), false)
				),
			],
			'ThirdPartyRisk.SecurityService' => [
				'name' => __('Treatment: Internal Controls'),
				'model' => 'SecurityService',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of a control, you can find them at Controls Catalogue / Internal Controls.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.SecurityPolicyTreatment' => [
				'name' => __('Treatment: Security Policies'),
				'model' => 'SecurityPolicyTreatment',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of a policy, you can find them at Controls Catalogue / Security Policies.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.RiskException' => [
				'name' => __('Treatment: Risk Exceptions'),
				'model' => 'RiskException',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of an exception, you can find them at Risk Management / Risk Exceptions.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.Project' => [
				'name' => __('Treatment: Projects'),
				'model' => 'Project',
				'headerTooltip' => __('Mandatory / optional depends on "Risk Treatment" input and settings of treatment options, you can find them in risk section settings under Treatment Options. Accepts multiple names separated by "|". You need to enter the name of a project, you can find them at Security Operations / Project Management.'),
				'objectAutoFind' => true
			],
			'ThirdPartyRisk.RiskClassificationTreatment' => [
				'name' => __('Risk Treatment Classification'),
				'model' => 'RiskClassificationTreatment',
				'headerTooltip' => $classificationDescription,
			],
			'ThirdPartyRisk.SecurityPolicyIncident' => [
				'name' => __('Risk Response Documents'),
				'model' => 'SecurityPolicyIncident',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a document, you can find them at Controls Catalogue / Security Policies.'),
				'objectAutoFind' => true
			],
		] + $residualScore;
	}

	public function beforeSave($options = array()) {
		$ret = true;

		if (isset($this->data['ThirdPartyRisk']['RiskClassificationTreatment']) && isset($this->data['ThirdPartyRisk']['Asset'])) {
			if ($this->getMethod() != 'magerit') {
				$this->data['ThirdPartyRisk']['RiskClassificationTreatment'] = array_filter($this->data['ThirdPartyRisk']['RiskClassificationTreatment']);
			}
		}

		$ret &= parent::beforeSave($options);

        // $this->transformDataToHabtm(array('ThirdParty', 'Asset', 'Threat', 'Vulnerability', 'SecurityService', 
        //     'RiskException', 'Project', 'SecurityPolicyIncident', 'SecurityPolicyTreatment', 'RiskClassification', 'RiskClassificationTreatment'
        // ));

        // $this->setHabtmConditionsToData(array('SecurityPolicyIncident', 'SecurityPolicyTreatment'));

		if (isset($this->data['ThirdPartyRisk']['risk_score']) && is_numeric($this->data['ThirdPartyRisk']['risk_score'])) {
			$this->data['ThirdPartyRisk']['risk_score'] = CakeNumber::precision($this->data['ThirdPartyRisk']['risk_score'], 2);
			$math = $this->getCalculationMath();
			if (!is_null($math)) {
				// $this->data['ThirdPartyRisk']['risk_score_formula'] = $math;
			}
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

	public function getControlsWithIssues($riskId, $find = 'list') {
		$this->SecurityServicesThirdPartyRisk->bindModel(array(
			'belongsTo' => array('SecurityService')
		));

		$ids = $this->SecurityServicesThirdPartyRisk->find('list', array(
			'conditions' => array(
				'SecurityServicesThirdPartyRisk.third_party_risk_id' => $riskId
			),
			'fields' => array('SecurityService.id'),
			'recursive' => 0
		));

		$issues = $this->SecurityService->getIssues($ids, $find);

		return $issues;
	}

	public function controlsIssuesMsgParams() {
		if (isset($this->data['ThirdPartyRisk']['id'])) {
			$issues = $this->getControlsWithIssues($this->data['ThirdPartyRisk']['id']);
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

	public function getThirdParties() {
		$data = $this->ThirdParty->find('list', array(
            'order' => array('ThirdParty.name' => 'ASC'),
            'fields' => array('ThirdParty.id', 'ThirdParty.name'),
            'recursive' => -1
        ));
        return $data;
	}

	public function getExceptionWithIssues($riskId, $find = 'list') {
		$this->RiskExceptionsThirdPartyRisk->bindModel(array(
			'belongsTo' => array('RiskException')
		));

		$issues = $this->RiskExceptionsThirdPartyRisk->find($find, array(
			'conditions' => array(
				'RiskExceptionsThirdPartyRisk.third_party_risk_id' => $riskId,
				'RiskException.expired' => 1
			),
			'fields' => array('RiskException.id', 'RiskException.title'),
			'recursive' => 0
		));

		return $issues;
	}

	public function exceptionsIssuesMsgParams() {

		if (isset($this->data['ThirdPartyRisk']['id'])) {
			$issues = $this->getExceptionWithIssues($this->data['ThirdPartyRisk']['id']);
		}
		elseif (isset($this->id)) {
			$issues = $this->getExceptionWithIssues($this->id);
		}

		if (!empty($issues)) {
			return implode(', ', $issues);
		}
	}

	public function queryControlsIssues() {
		if ($this->id != null && !isset($this->data['ThirdPartyRisk']['controls_issues'])) {

			if (!isset($this->data['ThirdPartyRisk']['security_service_id'])) {
				$this->SecurityServicesThirdPartyRisk->bindModel(array(
					'belongsTo' => array('SecurityService')
				));

				$ids = $this->SecurityServicesThirdPartyRisk->find('list', array(
					'conditions' => array(
						'SecurityServicesThirdPartyRisk.third_party_risk_id' => $this->id
					),
					'fields' => array('SecurityService.id'),
					'recursive' => 0
				));

				$this->data['ThirdPartyRisk']['security_service_id'] = $ids;
			}

			$data = $this->SecurityService->find('list', array(
				'conditions' => array(
					'OR' => array(
						array(
							'SecurityService.id' => $this->data['ThirdPartyRisk']['security_service_id'],
							'SecurityService.audits_all_done' => 0
						),
						array(
							'SecurityService.id' => $this->data['ThirdPartyRisk']['security_service_id'],
							'SecurityService.audits_last_passed' => 0
						)
					)
				),
				'fields' => array('SecurityService.id', 'SecurityService.name'),
				'recursive' => 0
			));

			if (!empty($data)) {
				$this->data['ThirdPartyRisk']['controls_issues'] = '1';

				return array(implode(', ', $data));
			}
			else {
				$this->data['ThirdPartyRisk']['controls_issues'] = '0';
			}
		}

		return null;
	}

	/**
	 * Callback used by Status Assessment to calculate expired field based on query data before saving and insert it into the query.
	 */
	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'review');
	}

	/**
	 * Calculate Risk Score for a Risk from given classification values.
	 * @return int Risk Score.
	 */
	public function calculateRiskScore($classification_ids = array(), $tp_ids = array()) {
		$this->resetCalculationClass();
		
		return $this->calculateByMethod(array(
			'classification_ids' => $classification_ids,
			'tp_ids' => $tp_ids,
		));
	}

	/**
	 * Calculate Risk Score for this Risk from given classification values.
	 * @return int Risk Score.
	 */
	public function ErambaCalculation($classification_ids = array(), $tp_ids = array()) {
		//$classification_ids = $this->request->data['ThirdPartyRisk']['risk_classification_id'];
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

		//$tp_ids = $this->request->data['ThirdPartyRisk']['third_party_id'];
		$tps = $this->ThirdParty->find('all', array(
			'conditions' => array(
				'ThirdParty.id' => $tp_ids
			),
			'fields' => array( 'id' ),
			'contain' => array(
				'Legal' => array(
					'fields' => array( 'id', 'risk_magnifier' )
				)
			)
		));

		return array($classifications, $tps);

		/*$classification_sum = 0;
		foreach ( $classifications as $classification ) {
			$classification_sum += $classification['RiskClassification']['value'];
		}

		$tp_sum = 0;
		foreach ( $tps as $tp ) {
			foreach ($tp['Legal'] as $legal) {
				$tp_sum += $legal['risk_magnifier'];
			}
		}

		if ( $tp_sum ) {
			return $classification_sum * $tp_sum;
		}

		return $classification_sum;*/
	}

	/**
	 * Calculate risk score and save to database.
	 */
	public function calculateAndSaveRiskScoreById($Ids) {
		$risks = $this->find('all', array(
			'conditions' => array(
				'ThirdPartyRisk.id' => $Ids
			),
			'contain' => array(
				'ThirdParty' => array(
					'fields' => array('id')
				),
				'RiskClassification' => array(
					'fields' => array('id')
				)
			)
		));

		$ret = true;
		foreach ($risks as $risk) {
			$classificationIds = array();
			foreach ($risk['RiskClassification'] as $c) {
				$classificationIds[] = $c['id'];
			}

			$tpIds = array();
			foreach ($risk['ThirdParty'] as $tp) {
				$tpIds[] = $tp['id'];
			}

			$riskScore = $this->calculateRiskScore($classificationIds, $tpIds);
			if (!is_numeric($riskScore)) {
				return false;
			}
			$residualRisk = getResidualRisk($risk['ThirdPartyRisk']['residual_score'], $riskScore);

			$saveData = array(
				'risk_score' => $riskScore,
				'residual_risk' => $residualRisk
			);

			$this->id = $risk['ThirdPartyRisk']['id'];
			$oldRiskScore = $this->field('risk_score');

			$ret &= (bool) $this->save($saveData, false);

			if (!empty($this->logAfterRiskScoreChange)) {
				$msg = $this->logAfterRiskScoreChange['message'];
				$args = $this->logAfterRiskScoreChange['args'];
				$args[] = $oldRiskScore;
				$args[] = $riskScore;

				array_unshift($args, $msg);
				$msg = call_user_func_array('sprintf', $args);
				$ret &= $this->quickLogSave($risk['ThirdPartyRisk']['id'], 2, $msg);
			}
		}

		return $ret;
	}

	public function controlsIssueConditions($data = array()){
		$conditions = array();
		if($data['controls_issues'] == 1){
			$conditions = array(
				'ThirdPartyRisk.controls_issues >' => 0
			);
		}
		elseif($data['controls_issues'] == 0){
			$conditions = array(
				'ThirdPartyRisk.controls_issues' => 0
			);
		}

		return $conditions;
	}

	public function residualRiskConditions($data = array()){
		$conditions = array();
		if($data['residual_risk'] == 1){
			$conditions = array(
				'ThirdPartyRisk.residual_risk >' => RISK_APPETITE
			);
		}
		elseif($data['residual_risk'] == 0){
			$conditions = array(
				'ThirdPartyRisk.residual_risk <=' => RISK_APPETITE
			);
		}

		return $conditions;
	}

	public function findByThirdParty($data = array()) {
        $this->ThirdPartiesThirdPartyRisk->Behaviors->attach('Containable', array(
                'autoFields' => false
            )
        );
        $this->ThirdPartiesThirdPartyRisk->Behaviors->attach('Search.Searchable');

        $query = $this->ThirdPartiesThirdPartyRisk->getQuery('all', array(
            'conditions' => array(
                'ThirdPartiesThirdPartyRisk.third_party_id' => $data['tp_id']
            ),
            'fields' => array(
                'ThirdPartiesThirdPartyRisk.third_party_risk_id'
            )
        ));

        return $query;
    }

    public function getThirdPartyIds($riskIds = array()) {
		$thirdPartyIds = $this->ThirdPartiesThirdPartyRisk->find('list', array(
			'conditions' => array(
				'ThirdPartiesThirdPartyRisk.third_party_risk_id' => $riskIds
			),
			'fields' => array(
				'ThirdPartiesThirdPartyRisk.third_party_id'
			)
		));

		return array_values($thirdPartyIds);
	}

}
