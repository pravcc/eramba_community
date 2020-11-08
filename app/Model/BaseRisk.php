<?php
App::uses('AppModel', 'Model');
App::uses('AppIndexCrudAction', 'Controller/Crud/Action');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('RiskClassification', 'Model');
App::uses('CustomValidatorField', 'CustomValidator.Model');
App::uses('UserFields', 'UserFields.Lib');
App::uses('RiskAppetite', 'Model');
App::uses('RiskAppetitesHelper', 'View/Helper');
App::uses('RiskCalculation', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('RiskAppetiteThreshold', 'Model');
App::uses('Hash', 'Utility');

abstract class BaseRisk extends AppModel
{
    public $actsAs = [
        'EventManager.EventManager',
        'Visualisation.Visualisation',
        'UserFields.UserFields' => [
            'fields' => ['Owner', 'Stakeholder']
        ],
        'ReviewsPlanner.Reviews' => [
            'dateColumn' => 'review',
            'userFields' => [
                'Stakeholder'
            ]
        ],
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report'
            ]
        ],
        'AdvancedQuery.AdvancedFinder',
        'CustomValidator.CustomValidator',
        'Comments.Comments',
        'Attachments.Attachments',
        'Widget.Widget',
        'Macros.Macro',
        'AdvancedFilters.AdvancedFilters',
        'CustomLabels.CustomLabels'
    ];

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

    public function __construct($id = false, $table = null, $ds = null)
    {
        //
        // Init helper Lib for UserFields Module
        $UserFields = new UserFields();
        //
        
        $this->fieldData = am($this->fieldData, [
            'title' => array(
                'label' => __('Title'),
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('Give this risk a descriptive title, for example "Loss of information due laptops being stolen".')
            ),
            'description' => array(
                'label' => __('Description'),
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('OPTIONAL: Describe this risk scenario, context, triggers, Etc.')
            ),
            'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
                'label' => __('Risk Owner'), 
                'description' => __('You can use this field in any way it fits best your organisation, for example:<br><br>

                                    - In some cases this role relates to the GRC individual responsible to ensure the Risk is well documented and approved  (this is typically our recommendation).<br> 
                                    - In some other organisations this role belongs to the individual that brings this organisation to the risk by performing a certain business function.<br><br> 

                                    This role will be available when you create notifications under the field Custom Roles.'),
                'quickAdd' => true,
                'inlineEdit' => true,
            ]),
            'Stakeholder' => $UserFields->getFieldDataEntityData($this, 'Stakeholder', [
                'label' => __('Stakeholder'), 
                'description' => __('Risk collaborators are those that generate risks by doing something or taking a specific decision. For example, all finance risks should have the finance team as collaborators.'),
                'quickAdd' => true,
                'inlineEdit' => true,
            ]),
            'Tag' => array(
                'type' => 'tags',
                'label' => __('Tags'),
                'editable' => true,
                'description' => __('OPTIONAL: Use tags to classify or tag your risk, examples are "Risk beign treated", "High Risk", "Financial Risk", Etc.'),
                'empty' => __('Add a tag')
            ),
            'review' => array(
                'label' => __('Review'),
                'editable' => false,
                'description' => __('Select a date in the future when this risk will be reviewed'),
                'renderHelper' => ['Risks', 'reviewField']
            ),
            'Threat' => array(
                'label' => __('Threat Tags'),
                'group' => 'analysis',
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('OPTIONAL: Select one or more applicable threats tags.'),
                'renderHelper' => ['Risks', 'threatField'],
                'quickAdd' => true,
            ),
            'threats' => array(
                'label' => __('Threat Description'),
                'group' => 'analysis',
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('OPTIONAL: Describe the context of the threats vectors for this risk.'),
                'macro' => [
                    'name' => 'threat_description'
                ]
            ),
            'Vulnerability' => array(
                'label' => __('Vulnerabilities Tags'),
                'group' => 'analysis',
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('OPTIONAL: Select one or more applicable vulnerability tags.'),
                'renderHelper' => ['Risks', 'vulnerabilityField'],
                'quickAdd' => true,
            ),
            'vulnerabilities' => array(
                'label' => __('Vulnerabilities Description'),
                'group' => 'analysis',
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('OPTIONAL: Describe the context of the vulnerabilities vectors for this risk.'),
                'macro' => [
                    'name' => 'vulnerability_description'
                ]
            ),
            'RiskClassification' => array(
                'type' => 'select',
                'label' => __('Risk Anaylsis Classification'),
                'group' => 'analysis',
                'options' => array($this, 'getClassifications'),
                'editable' => false,
                'empty' => __('Choose Classification'),
                'renderHelper' => ['Risks', 'riskClassificationField'],
                'Extensions' => [
                    'RiskClassification'
                ],
                'description' => __('eramba will display your risk classification options as per the settings defined at Risk Management / Asset Risk Management / Settings / Classification and the Risk Calculation method choosen at Risk Management / Asset Risk Management / Settings / Risk Calculation.'),
                'macro' => [
                    'name' => 'classification'
                ]
            ),
            'risk_mitigation_strategy_id' => array(
                'label' => __('Risk Treatment'),
                'group' => 'treatment',
                'editable' => true,
                'inlineEdit' => false,
                'description' => __('Select a treatment strategy for this risk. Treatment options can be adjusted at Settings / Risk Treatment Options'),
                'renderHelper' => ['Risks', 'riskMitigationStrategyField'],
                'macro' => [
                    'name' => 'treatment'
                ]
            ),
            'SecurityService' => array(
                'label' => __('Treatment: Internal Controls'),
                'group' => 'treatment',
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('Select one or more controls defined at Control Catalogue / Internal Controls'),
                'renderHelper' => ['Risks', 'securityServiceField'],
                'quickAdd' => true,
            ),
            'SecurityPolicyTreatment' => array(
                'label' => __('Treatment: Security Policies'),
                'group' => 'treatment',
                'options' => array($this, 'getSecurityPolicies'),
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('Select one or more documents defined at Control Catalogue / Security Policies.'),
                'quickAdd' => true,
                'macro' => [
                    'name' => 'treatment_document'
                ]
            ),
            'RiskException' => array(
                'label' => __('Treatment: Risk Exceptions'),
                'group' => 'treatment',
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('Select one or more Risk Exceptions (Risk Management / Risk Exceptions).'),
                'quickAdd' => true,
            ),
            'Project' => array(
                'label' => __('Treatment: Projects'),
                'group' => 'treatment',
                'editable' => true,
                'inlineEdit' => true,
                'options' => array($this, 'getProjectsNotCompleted'),
                'description' => __('Select one ore more projects (Security Operations / Project Management).'),
                'quickAdd' => true,
            ),
            'RiskClassificationTreatment' => array(
                'type' => 'select',
                'label' => __('Risk Treatment Classification'),
                'group' => 'treatment',
                'options' => array($this, 'getClassifications'),
                'editable' => false,
                'empty' => __('Choose Classification'),
                'renderHelper' => ['Risks', 'riskClassificationField'],
                'Extensions' => [
                    'RiskClassification'
                ],
                'description' => __('eramba will display your risk classification options as per the settings defined at Risk Management / Asset Risk Management / Settings / Classification and the Risk Calculation method choosen at Risk Management / Asset Risk Management / Settings / Risk Calculation.'),
                'macro' => [
                    'name' => 'classification_treatment'
                ]
            ),
            'SecurityPolicyIncident' => array(
                'label' => __('Risk Response Documents'),
                'group' => 'response-plan',
                'options' => array($this, 'getSecurityPolicies'),
                'editable' => true,
                'inlineEdit' => true,
                'description' => __('OPTIONAL: Select one or more documents (defined in Control Catalogue / Security Policies) that will be used when recording incidents on the Incident module located at Security Operations / Incident Management'),
                'quickAdd' => true,
                'macro' => [
                    'name' => 'risk_response_document'
                ]
            ),
            'residual_score' => array(
                'label' => __('Residual Score'),
                'group' => 'treatment',
                'editable' => true,
                'options' => 'getReversePercentageOptions',
                'description' => __('Select the percentage of Risk Reduction that was achieved by applying Security Controls. If the risk score for this risk is 100 points and you select %100, the residual for this risk will be 100 points. If you choose %30, the residual will be 30.'),
                'renderHelper' => ['Risks', 'residualScoreField']
            ),
            'expired' => array(
                'label' => __('Expired'),
                'type' => 'toggle',
                'hidden' => true,
            ),
            'exceptions_issues' => array(
                'label' => __('Exception Issues'),
                'type' => 'toggle',
                'hidden' => true
            ),
            'controls_issues' => array(
                'label' => __('Controls Issues'),
                'type' => 'toggle',
                'hidden' => true
            ),
            'control_in_design' => array(
                'label' => __('Controls in Design'),
                'type' => 'toggle',
                'hidden' => true
            ),
            'expired_reviews' => array(
                'label' => __('Expired Reviews'),
                'type' => 'toggle',
                'hidden' => true
            ),
            'risk_above_appetite' => array(
                'label' => __('Risk above appetite'),
                'type' => 'toggle',
                'hidden' => true
            ),
            'DataAsset' => [
                'label' => __('Data Assets Flows'),
                'editable' => false
            ],
            'risk_score' => [
                'label' => __('Risk Score'),
                'editable' => false
            ],
            'residual_risk' => [
                'label' => __('Residual Risk'),
                'editable' => false
            ],
            'RiskAppetiteThresholdTreatment' => array(
                'options' => array($this, 'getThresholds'),
                'label' => __('Treatment Risk Appetite'),
                'hidden' => true
            ),
            'RiskAppetiteThresholdAnalysis' => array(
                'options' => array($this, 'getThresholds'),
                'label' => __('Analysis Risk Appetite'),
                'hidden' => true
            ),
            'SecurityIncident' => array(
                'label' => __('Security Incident'),
                'editable' => false,
                'hidden' => true
            ),
            'ComplianceManagement' => array(
                'label' => __('Compliance Analysis'),
                'editable' => false,
                'hidden' => true
            ),            
        ]);

        parent::__construct($id, $table, $ds);
    }

    public function _getAdvancedFilterConfig()
    {
        $riskAppetiteType = $this->getRiskAppetiteType();

        $advancedFilterConfig = $this->createAdvancedFilterConfig()
            ->group('general', [
                'name' => __('General')
            ])
                ->nonFilterableField('id')
                ->textField('title', [
                    'showDefault' => true
                ])
                ->textField('description', [
                    'showDefault' => true
                ])
                ->multipleSelectField('Tag-title', [$this, 'getTags'])
                ->userField('Stakeholder', 'Stakeholder', [
                    'showDefault' => true
                ])
                ->userField('Owner', 'Owner', [
                    'showDefault' => true
                ])
                ->dateField('review', [
                    'label' => __('Review Date'),
                    'showDefault' => true
                ]);

                if ($riskAppetiteType != RiskAppetite::TYPE_THRESHOLD) {
                    $advancedFilterConfig->objectStatusField('ObjectStatus_risk_above_appetite', 'risk_above_appetite', [
                        'showDefault' => true
                    ]);
                }

                $advancedFilterConfig
                ->objectStatusField('ObjectStatus_expired_reviews', 'expired_reviews', [
                    'showDefault' => true
                ])
            ->group('analysis', [
                'name' => __('Risk Analysis')
            ])
                ->multipleSelectField('RiskClassification', [$this, 'getClassifications'], [
                    'label' => __('Risk Anaylsis Classification'),
                    'showDefault' => true
                ]);

                if ($riskAppetiteType == RiskAppetite::TYPE_THRESHOLD) {
                    $advancedFilterConfig->multipleSelectField('RiskAppetiteThresholdAnalysis', [$this, 'getThresholds'], [
                        'label' => __('Risk Analysis Appetite'),
                    ]);
                }

                $advancedFilterConfig
                ->numberField('risk_score', [
                    'label' => __('Risk Analysis Score'),
                    'showDefault' => true
                ])
                ->multipleSelectField('Threat', [ClassRegistry::init('Threat'), 'getList'])
                ->textField('threats')
                ->multipleSelectField('Vulnerability', [ClassRegistry::init('Vulnerability'), 'getList'])
                ->textField('vulnerabilities')
            ->group('treatment', [
                'name' => __('Risk Treatment')
            ])
                ->multipleSelectField('RiskClassificationTreatment', [$this, 'getClassifications'], [
                    'label' => __('Risk Treatment Classification'),
                    'showDefault' => true
                ]);

                if ($riskAppetiteType == RiskAppetite::TYPE_THRESHOLD) {
                    $advancedFilterConfig->multipleSelectField('RiskAppetiteThresholdTreatment', [$this, 'getThresholds'], [
                        'label' => __('Risk Treatment Appetite'),
                    ]);
                }

                $advancedFilterConfig
                ->numberField('residual_risk', [
                    'label' => __('Risk Treatment Score'),
                    'showDefault' => true
                ])
                ->multipleSelectField('risk_mitigation_strategy_id', [$this, 'getRiskStrategies'], [
                    'showDefault' => true
                ])
                ->multipleSelectField('RiskException-id', [ClassRegistry::init('RiskException'), 'getList'], [
                    'label' => __('Treatment Risk Exception'),
                    'fieldData' => 'RiskException'
                ])
                ->multipleSelectField('SecurityService-id', [ClassRegistry::init('SecurityService'), 'getList'], [
                    'label' => __('Treatment Internal Control'),
                    'fieldData' => 'SecurityService'
                ])
                ->multipleSelectField('Project-id', [ClassRegistry::init('Project'), 'getList'], [
                    'label' => __('Treatment Project'),
                    'fieldData' => 'Project'
                ])
                ->multipleSelectField('SecurityPolicyTreatment-id', [ClassRegistry::init('SecurityPolicy'), 'getList'], [
                    'label' => __('Treatment Security Policy'),
                    'fieldData' => 'SecurityPolicyTreatment'
                ])
            ->group('DataAsset', [
                'name' => __('Data Flow Analysis')
            ])
                ->multipleSelectField('DataAssetInstance-asset_id', [ClassRegistry::init('Asset'), 'getList'], [
                    'label' => __('Data Asset'),
                    'findField' => 'DataAsset.DataAssetInstance.asset_id',
                    'fieldData' => 'DataAsset.DataAssetInstance.asset_id'
                ])
                ->multipleSelectField('DataAsset', [ClassRegistry::init('Risk'), 'getList'], [
                    'label' => __('Data Asset Flow')
                ])
                ->multipleSelectField('DataAsset-data_asset_status_id', [ClassRegistry::init('DataAsset'), 'statuses'], [
                    'label' => __('Data Asset Flow Type')
                ])
            ->group('SecurityIncident', [
                'name' => __('Security Incident')
            ])
                ->multipleSelectField('SecurityIncident', [ClassRegistry::init('SecurityIncident'), 'getList'])
                ->selectField('SecurityIncident-security_incident_status_id', [ClassRegistry::init('SecurityIncident'), 'statuses'], [
                    'label' => __('Security Incident Status')
                ]);

        $this->ComplianceManagement->relatedFilters($advancedFilterConfig);

        $advancedFilterConfig
            ->group('Project', [
                'name' => __('Project Management')
            ])
                ->multipleSelectField('Project', [ClassRegistry::init('Project'), 'getList'], [
                    'label' => __('Project')
                ])
                ->textField('ProjectAchievement-description', [
                    'label' => __('Project Task'),
                    'findField' => 'Project.ProjectAchievement.description',
                    'fieldData' => 'Project.ProjectAchievement'
                ])
                ->objectStatusField('ObjectStatus_project_planned', 'project_planned')
                ->objectStatusField('ObjectStatus_project_ongoing', 'project_ongoing')
                ->objectStatusField('ObjectStatus_project_closed', 'project_closed')
                ->objectStatusField('ObjectStatus_project_expired', 'project_expired')
                ->objectStatusField('ObjectStatus_project_expired_tasks', 'project_expired_tasks')
            ->group('SecurityService', [
                'name' => __('Internal Control')
            ])
                ->multipleSelectField('SecurityService', [ClassRegistry::init('SecurityService'), 'getList'], [
                    'label' => __('Internal Control')
                ])
                ->textField('SecurityService-objective', [
                    'label' => __('Internal Control Description')
                ])
                ->objectStatusField('ObjectStatus_audits_last_not_passed', 'audits_last_not_passed')
                ->objectStatusField('ObjectStatus_audits_last_missing', 'audits_last_missing')
                ->objectStatusField('ObjectStatus_control_with_issues', 'control_with_issues')
                ->objectStatusField('ObjectStatus_maintenances_last_missing', 'maintenances_last_missing')
            ->group('SecurityPolicy', [
                'name' => __('Security Policy')
            ])
                ->multipleSelectField('SecurityPolicyTreatment', [ClassRegistry::init('SecurityPolicy'), 'getList'], [
                    'label' => __('Security Policy')
                ])
            ->group('RiskException', [
                'name' => __('Risk Exception')
            ])
                ->multipleSelectField('RiskException', [ClassRegistry::init('RiskException'), 'getList'], [
                    'label' => __('Exception')
                ])
                ->textField('RiskException-description', [
                    'label' => __('Exception Description')
                ])
                ->selectField('RiskException-status', [ClassRegistry::init('RiskException'), 'statuses'], [
                    'label' => __('Exception Status')
                ])
                ->objectStatusField('ObjectStatus_risk_exception_expired', 'risk_exception_expired');

        if (AppModule::loaded('CustomFields')) {
            $this->customFieldsFilters($advancedFilterConfig);
        }

        $this->otherFilters($advancedFilterConfig);

        return $advancedFilterConfig;
    }

    public function getObjectStatusConfig() {
        return [
            'expired' => [// delete
                'title' => __('Review Expired'),
                'callback' => [$this, 'statusExpired'],
                'hidden' => true,
                'regularTrigger' => true,
            ],
            'current_review_trigger' => [
                'trigger' => [
                    [
                        'model' => $this->{$this->alias . 'Review'},
                        'trigger' => 'ObjectStatus.trigger.current_review'
                    ],
                ],
                'hidden' => true
            ],
            'exceptions_issues' => [// delete
                'title' => __('Exception Issues'),
                'callback' => [$this, 'statusExceptionsIssues'],
                'type' => 'danger',
                'hidden' => true // its same status like risk_exception_expired
            ],
            'controls_issues' => [// delete
                'title' => __('Control Issues'),
                'inherited' => [
                    'SecurityService' => 'control_with_issues'
                ],
                'type' => 'danger',
                'hidden' => true // its same status like control_with_issues
            ],
            'control_in_design' => [
                'title' => __('Control In Design'),
                'inherited' => [
                    'SecurityService' => 'control_in_design'
                ],
            ],
            'ongoing_incident' => [
                'title' => __('Incident Ongoing'),
                'inherited' => [
                    'SecurityIncident' => 'ongoing_incident'
                ],
                'storageSelf' => false,
            ],
            'risk_exception_expired' => [
                'title' => __('Exception Expired'),
                'inherited' => [
                    'RiskException' => 'expired'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'audits_last_not_passed' => [
                'title' => __('Last Audit Failed'),
                'inherited' => [
                    'SecurityService' => 'audits_last_not_passed'
                ],
                'type' => 'danger',
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'audits_last_missing' => [
                'title' => __('Last Audit Expired'),
                'inherited' => [
                    'SecurityService' => 'audits_last_missing'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'maintenances_last_missing' => [
                'title' => __('Last Maintenance Expired'),
                'inherited' => [
                    'SecurityService' => 'maintenances_last_missing'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'control_with_issues' => [
                'title' => __('Control Issues'),
                'inherited' => [
                    'SecurityService' => 'control_with_issues'
                ],
                'type' => 'danger',
                'storageSelf' => false,
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

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function mitigationStrategies($value = null) {
        $options = array(
            self::RISK_MITIGATION_ACCEPT => __('Accept'),
            self::RISK_MITIGATION_AVOID => __('Avoid'),
            self::RISK_MITIGATION_MITIGATE => __('Mitigate'),
            self::RISK_MITIGATION_TRANSFER => __('Transfer')
        );
        return parent::enum($value, $options);
    }

    const RISK_MITIGATION_ACCEPT = RISK_MITIGATION_ACCEPT;
    const RISK_MITIGATION_AVOID = RISK_MITIGATION_AVOID;
    const RISK_MITIGATION_MITIGATE = RISK_MITIGATION_MITIGATE;
    const RISK_MITIGATION_TRANSFER = RISK_MITIGATION_TRANSFER;

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
            $this->alias . '.review < DATE(NOW())'
        ]);
    }

    public function _statusRiskAboveAppetite($id = null, $dbValue = false) {
        $appetiteMethod = $this->getRiskAppetiteType();
        if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
            return false;
        }

        $id = ($id === null) ? $this->id : $id;
        
        $data = $this->find('count', [
            'conditions' => [
                $this->alias . '.id' => $id,
                $this->alias . '.residual_risk >' => ClassRegistry::init('Setting')->getVariable('RISK_APPETITE')
            ],
            'recursive' => -1
        ]);

        return (boolean) $data;
    }

    public function getRiskAppetiteType() {
        $RiskAppetite = ClassRegistry::init('RiskAppetite');

        return $RiskAppetite->getCurrentType();
    }

    public function getAttachedClassifications($types = false)
    {
        $calculations = ClassRegistry::init('RiskCalculation')->find('list', [
            'conditions' => [
                'RiskCalculation.model' => $this->name
            ],
            'fields' => [
                'RiskCalculation.id'
            ]
        ]);

        $values = ClassRegistry::init('RiskCalculationValue')->find('list', [
            'conditions' => [
                'RiskCalculationValue.risk_calculation_id' => $calculations
            ],
            'fields' => [
                'RiskCalculationValue.id', 'RiskCalculationValue.value'
            ]
        ]);

        $classifications = ClassRegistry::init('RiskClassification')->find('all', [
            'conditions' => [
                'RiskClassification.risk_classification_type_id' => $values,
            ],
            'order' => [
                'RiskClassification.value' => 'ASC'
            ],
            'contain' => [
                'RiskClassificationType'
            ]
        ]);

        $data = [];

        foreach ($classifications as $item) {
            if ($types) {
                $data[$item['RiskClassificationType']['id']] = $item['RiskClassificationType']['name'];
            }
            else {
                $data[$item['RiskClassificationType']['id']][$item['RiskClassification']['id']] = $item['RiskClassification']['name'];
            }
        }

        ksort($data);

        return $data;
    }

    public function getAttachedTresholds()
    {
        $classificationTypes = array_values($this->getAttachedClassifications());

        $default = ClassRegistry::init('RiskAppetiteThreshold')->find('first', [
            'conditions' => [
                'RiskAppetiteThreshold.type' => RiskAppetiteThreshold::TYPE_DEFAULT
            ],
        ]);

        $tresholdsRaw = ClassRegistry::init('RiskAppetiteThreshold')->find('all', [
            'conditions' => [
                'RiskAppetiteThreshold.type' => RiskAppetiteThreshold::TYPE_GENERAL
            ],
        ]);

        $tresholds = [];

        foreach ($tresholdsRaw as $key => $item) {
            $tresholds[$item['RiskAppetiteThresholdClassification'][0]['risk_classification_id']][$item['RiskAppetiteThresholdClassification'][1]['risk_classification_id']] = $item;
        }

        $x = 0;

        $data = [];

        foreach ($classificationTypes[0] as $keyX => $classificationX) {
            $y = 0;

            foreach ($classificationTypes[1] as $keyY => $classificationY) {
                if (isset($tresholds[$keyX][$keyY])) {
                    $item = [
                        $x, $y, $tresholds[$keyX][$keyY]['RiskAppetiteThreshold']['title'], $tresholds[$keyX][$keyY]['RiskAppetiteThreshold']['color']
                    ];
                }
                elseif (isset($tresholds[$keyY][$keyX])) {
                    $item = [
                        $x, $y, $tresholds[$keyY][$keyX]['RiskAppetiteThreshold']['title'], $tresholds[$keyY][$keyX]['RiskAppetiteThreshold']['color']
                    ]; 
                }
                else {
                    $item = [
                        $x, $y, $default['RiskAppetiteThreshold']['title'], $default['RiskAppetiteThreshold']['color']
                    ]; 
                }
                
                $data[] = $item;

                $y++;
            }

            $x++;
        }

        return $data; 
    }

    public function getNotificationSystemConfig()
    {
        $config = parent::getNotificationSystemConfig();
        $config['notifications'] = array_merge($config['notifications'], [

        ]);

        return $config;
    }

    public function getReportsConfig()
    {
        $riskMatrix = [
            'title' => __('Risk Matrix'),
            'description' => __('This chart shows risks based on their classification, this chart does not contemplate thresholds.'),
            'type' => ReportBlockChartSetting::TYPE_MATRIX,
            'dataFn' => 'classificationsMatrixChart',
        ];

        $riskMatrixTreshold = [
            'title' => __('Risk Matrix (Thresholds)'),
            'description' => __('This chart shows risks based on their classification, the matrix includes the description and colour of thresholds.'),
            'type' => ReportBlockChartSetting::TYPE_MATRIX,
            'dataFn' => 'classificationsTresholdsMatrixChart',
        ];

        return [
            'chart' => [
                11 => [
                    'title' => __('Risks by Treatment Option'),
                    'description' => __('This chart shows the amount of risks by treatment option.'),
                    'type' => ReportBlockChartSetting::TYPE_RADAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'mitigationStrategiesChart'
                ],
                12 => [
                    'title' => __('Risks by Tags'),
                    'description' => __('This chart shows risks based on their assigned tags.'),
                    'type' => ReportBlockChartSetting::TYPE_DOUGHNUT,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'tagsChart'
                ],
                13 => [
                    'title' => __('Risk Score and Residual over time'),
                    'description' => __('This chart shows the amount of risk and residual over time.'),
                    'type' => ReportBlockChartSetting::TYPE_LINE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'riskScoreChart',
                ],
                14 => array_merge($riskMatrix, [
                    'templateType' => ReportTemplate::TYPE_SECTION
                ]),
                15 => array_merge($riskMatrix, [
                    'templateType' => ReportTemplate::TYPE_ITEM
                ]),
                16 => array_merge($riskMatrixTreshold, [
                    'templateType' => ReportTemplate::TYPE_SECTION
                ]),
                17 => array_merge($riskMatrixTreshold, [
                    'templateType' => ReportTemplate::TYPE_ITEM
                ]),
                18 => [
                    'title' => __('Top 20 Risk Owners'),
                    'description' => __('The chart shows the top 20 risk owners.'),
                    'type' => ReportBlockChartSetting::TYPE_PIE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'topRiskOwnersChart',
                ],
                19 => [
                    'title' => __('Top 20 Risk Stakeholders'),
                    'description' => __('The chart shows the top 20 risk stakeholders.'),
                    'type' => ReportBlockChartSetting::TYPE_PIE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'topRiskStakeholdersChart',
                ],
                20 => [
                    'title' => __('Accumulated Risk by Owner'),
                    'description' => __('This chart shows risk score grouped by Risk Owner.'),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'riskScoreByOwnerChart',
                ],
                21 => [
                    'title' => __('Accumulated Risk by Stakeholder'),
                    'description' => __('This chart shows risk score grouped by Risk Stakeholder.'),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'riskScoreByStakeholderChart',
                ],
                22 => [
                    'title' => __('Risk Costs CAPEX'),
                    'description' => __(''),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'RiskCostsChart',
                    'params' => [
                        'field' => 'capex',
                    ]
                ],
                23 => [
                    'title' => __('Risk Costs CAPEX'),
                    'description' => __(''),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_ITEM,
                    'className' => 'RiskCostsChart',
                    'params' => [
                        'field' => 'capex',
                    ]
                ],
                24 => [
                    'title' => __('Risk Costs OPEX'),
                    'description' => __(''),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'RiskCostsChart',
                    'params' => [
                        'field' => 'opex',
                    ]
                ],
                25 => [
                    'title' => __('Risk Costs OPEX'),
                    'description' => __(''),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_ITEM,
                    'className' => 'RiskCostsChart',
                    'params' => [
                        'field' => 'opex',
                    ]
                ],
                26 => [
                    'title' => __('Risks by Status'),
                    'description' => __('This chart shows risks by their associated treatment options status, no that risks can have more than one status and therefore you might have more items in the pie than actual number of risks.'),
                    'type' => ReportBlockChartSetting::TYPE_PIE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'ItemsByStatusChart',
                ],
                27 => [
                    'title' => __('Top 10 Threats'),
                    'description' => __('The chart shows the top 10 used threats.'),
                    'type' => ReportBlockChartSetting::TYPE_PIE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'MostUsedRelatedObjectsChart',
                    'params' => [
                        'field' => 'Threat'
                    ]
                ],
                28 => [
                    'title' => __('Top 10 Vulnerabilities'),
                    'description' => __('The chart shows the top 10 used vulnerabilities.'),
                    'type' => ReportBlockChartSetting::TYPE_PIE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'MostUsedRelatedObjectsChart',
                    'params' => [
                        'field' => 'Vulnerability'
                    ]
                ],
                29 => [
                    'title' => __('Top 10 Tags'),
                    'description' => __('The chart shows the top 10 used tags.'),
                    'type' => ReportBlockChartSetting::TYPE_PIE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'MostUsedRelatedObjectsChart',
                    'params' => [
                        'field' => 'Tag'
                    ]
                ],
            ]
        ];
    }

    public function getCustomValidatorConfig() {
        $defaultFieldsConfig = [
            'SecurityService' => CustomValidatorField::OPTIONAL_VALUE,
            'SecurityPolicyTreatment' => CustomValidatorField::OPTIONAL_VALUE,
            'RiskException' => 'minCount',
            'Project' => CustomValidatorField::OPTIONAL_VALUE
        ];

        return [
            'mitigation_strategy_accept' => [
                'title' => __('Risk Treatment Validation - %s', self::mitigationStrategies(self::RISK_MITIGATION_ACCEPT)), 
                'conditions' => [
                    'risk_mitigation_strategy_id' => self::RISK_MITIGATION_ACCEPT
                ],
                'fields' => $defaultFieldsConfig,
            ],
            'mitigation_strategy_avoid' => [
                'title' => __('Risk Treatment Validation - %s', self::mitigationStrategies(self::RISK_MITIGATION_AVOID)), 
                'conditions' => [
                    'risk_mitigation_strategy_id' => self::RISK_MITIGATION_AVOID
                ],
                'fields' => $defaultFieldsConfig,
            ],
            'mitigation_strategy_mitigate' => [
                'title' => __('Risk Treatment Validation - %s', self::mitigationStrategies(self::RISK_MITIGATION_MITIGATE)), 
                'conditions' => [
                    'risk_mitigation_strategy_id' => self::RISK_MITIGATION_MITIGATE
                ],
                'fields' => [
                    'SecurityService' => 'minCount',
                    'SecurityPolicyTreatment' => CustomValidatorField::OPTIONAL_VALUE,
                    'RiskException' => CustomValidatorField::OPTIONAL_VALUE,
                    'Project' => CustomValidatorField::OPTIONAL_VALUE
                ],
            ],
            'mitigation_strategy_transfer' => [
                'title' => __('Risk Treatment Validation - %s', self::mitigationStrategies(self::RISK_MITIGATION_TRANSFER)), 
                'conditions' => [
                    'risk_mitigation_strategy_id' => self::RISK_MITIGATION_TRANSFER
                ],
                'fields' => $defaultFieldsConfig,
            ],
        ];
    }

    /**
     * OverSave RiskClassification join data. 
     * NOTE: We need to save duplicit HABTM records and its not supported by Cake.
     */
    public function saveRiskClassifications($data, $model = 'RiskClassification') {
        if (!isset($data[$model])) {
            return true;
        }

        $ret = true;

        $assoc = $this->getAssociated($model);
        $WithModel = $this->{$assoc['with']};

        // prepare general conditions for the association
        $conds = Hash::expand($assoc['conditions']);
        $conds = reset($conds);

        //delete all existing records
        $conds[$assoc['foreignKey']] = $this->id;
        $ret &= $WithModel->deleteAll($conds);

        if (isset($data[$model][$model]) && is_array($data[$model][$model])) {
            //save new records
            foreach ($data[$model][$model] as $item) {
                $WithModel->create();
                $saveConds = $conds;
                $saveConds[$assoc['associationForeignKey']] = $item[$assoc['associationForeignKey']];
                $ret &= $WithModel->save($saveConds);
            }
        }

        return $ret;
    }

    public function beforeValidate($options = array()) {
        $ret = parent::beforeValidate($options);

        $calculationMethod = $this->Behaviors->RiskCalculationManager->getMethod($this);
        $appetiteMethod = ClassRegistry::init('RiskAppetite')->getCurrentType();

        $validateAnalysis = $calculationMethod != RiskCalculation::METHOD_MAGERIT;
        if ($validateAnalysis) {
            // validate classifications generally equally for each risk section
            $this->_validateClassification('RiskClassification');
        }

        $validateTreatment = $appetiteMethod === RiskAppetite::TYPE_THRESHOLD;
        $validateTreatment &= $calculationMethod != RiskCalculation::METHOD_MAGERIT;
        if ($validateTreatment) {
            $this->_validateClassification('RiskClassificationTreatment');
        }

        if (!empty($options['import'])) {
            $this->setCustomValidator($this->data);
        }

        if ($this->getRiskAppetiteType() == RiskAppetite::TYPE_THRESHOLD) {
            $this->validate['residual_score']['required'] = false;
            $this->validate['residual_score']['allowEmpty'] = true;
        }

        return $ret;
    }

    protected function _validateClassification($type) {
        if (!isset($this->data[$this->alias][$type])) {
            return true;
        }

        $countData = 0;
        if (!empty($this->data[$this->alias][$type])) {
            $countData = count(array_filter($this->data[$this->alias][$type]));
        }

        $classifications = $this->getFormClassifications();
        $countClassifications = count($classifications);

        if ($countData !== $countClassifications) {
            $this->invalidate($type, __('You have to select all Risk Classifications to meet required conditions'));
        }

        foreach ($this->data[$this->alias][$type] as $key => $value) {
            $allowedIds = Hash::extract($classifications[$key], 'RiskClassification.{n}.id');

            if (!in_array($value, $allowedIds)) {
                $this->invalidate($type, __('Provided value is invalid, also check order of provided values.'));
            }
        }
    }

    public function afterSave($created, $options = array()) {
        $ret = true;

        //OverSave RiskClassification join data. We need to save duplicit HABTM records and its not supported by Cake. 
        $this->saveRiskClassifications($this->data, 'RiskClassification');

        //OverSave RiskClassification join data. We need to save duplicit HABTM records and its not supported by Cake. 
        $this->saveRiskClassifications($this->data, 'RiskClassificationTreatment');

        parent::afterSave($created, $options);

        return $ret;
    }

    public function saveRiskScoreWrapper($id) {
        $this->afterSaveRiskScore($id, $this->scoreAssocModel);

        $riskAppetiteMethod = ClassRegistry::init('RiskAppetite')->getCurrentType();

        if ($riskAppetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
            $RiskThresholdRisks = ClassRegistry::init('RiskAppetiteThresholdsRisk');

            $threshold = $this->riskThreshold($id, RiskClassification::TYPE_ANALYSIS);
            $RiskThresholdRisks->saveItem($this, $id, $threshold['RiskAppetiteThreshold']['id'], RiskClassification::TYPE_ANALYSIS);

            $threshold = $this->riskThreshold($id, RiskClassification::TYPE_TREATMENT);
            $RiskThresholdRisks->saveItem($this, $id, $threshold['RiskAppetiteThreshold']['id'], RiskClassification::TYPE_TREATMENT);
        }
    }

    // used in after save for recalculation
    public function afterSaveRiskScore($id, $assocModel) {
        $risk = $this->find('first', array(
            'conditions' => array(
                "{$this->alias}.id" => $id
            ),
            'contain' => array(
                $assocModel => array(
                    'fields' => array('id')
                ),
                'RiskClassification' => array(
                    'fields' => array('id')
                ),
                'RiskClassificationTreatment' => [
                    'fields' => ['id']
                ]
            )
        ));

        $classificationIds = array();
        foreach ($risk['RiskClassification'] as $c) {
            $classificationIds[] = $c['id'];
        }

        $assocIds = array();
        foreach ($risk[$assocModel] as $a) {
            $assocIds[] = $a['id'];
        }

        $riskScore = $this->calculateRiskScore($classificationIds, $assocIds);
        $residualScore = isset($this->data[$this->alias]['residual_score']) ? $this->data[$this->alias]['residual_score'] : $risk[$this->alias]['residual_score'];

        $saveData = [];
        if (is_numeric($riskScore)) {
            $math = $this->getCalculationMath();
            if (!is_null($math)) {
                $saveData['risk_score_formula'] = $math;
            }

            $riskAppetiteMethod = ClassRegistry::init('RiskAppetite')->getCurrentType();
            if ($riskAppetiteMethod == RiskClassification::TYPE_ANALYSIS) {
                $residualRisk = getResidualRisk($residualScore, $riskScore);
            }

            if ($riskAppetiteMethod == RiskClassification::TYPE_TREATMENT) {
                $treatmentIds = array();
                foreach ($risk['RiskClassificationTreatment'] as $c) {
                    $treatmentIds[] = $c['id'];
                }

                $residualRisk = $this->calculateRiskScore($treatmentIds, $assocIds);
                $math = $this->getCalculationMath();
                 if (!is_null($math)) {
                    $saveData['residual_risk_formula'] = $math;
                }
            }

            $saveData['id'] = $id;
            $saveData['risk_score'] = $riskScore;
            $saveData['residual_risk'] = $residualRisk;

            $this->create();
            $this->id = $saveData['id'];
            $this->set($saveData);
            $ret = $this->save($saveData, ['validate' => false, 'fieldList' => array_keys($saveData), 'callbacks' => 'before']);
        }
    }

    public function getDataAssets() {
        return $this->DataAsset->getList();
    }

    public function getAssets() {
        $assets = $this->Asset->find('list', array(
            'fields' => array('Asset.name'),
        ));

        return $assets;
    }

    public function getThreats() {
        $data = $this->Threat->find('list', array(
            'fields' => array('Threat.name'),
            'order' => array('Threat.name' => 'ASC')
        ));

        return $data;
    }

    public function getVulnerabilities() {
        $data = $this->Vulnerability->find('list', array(
            'fields' => array('Vulnerability.name'),
            'order' => array('Vulnerability.name' => 'ASC')
        ));

        return $data;
    }

    public function getException() {
        $exceptions = $this->RiskException->find('list', array(
            'order' => array('RiskException.title' => 'ASC'),
            'fields' => array('RiskException.id', 'RiskException.title'),
            'recursive' => -1
        ));
        return $exceptions;
    }

    public function getRiskStrategies() {
        $strategies = $this->RiskMitigationStrategy->find('list', array(
            'fields' => array('RiskMitigationStrategy.id', 'RiskMitigationStrategy.name')
        ));

        return $strategies;
    }

    public function getServices() {
        $services = $this->SecurityService->find('list', array(
            'fields' => array('SecurityService.id', 'SecurityService.name')
        ));

        return $services;
    }

    public function getProjects() {
        return $this->Project->getList(false);
    }

    public function getProjectsNotCompleted() {
        return $this->Project->getList();
    }

    public function getSecurityPolicies() {
        return $this->SecurityPolicy->getListWithType();
    }

    public function getBusinessUnits() {
        $data = $this->Asset->BusinessUnit->find('list', array(
            'fields' => array('BusinessUnit.id', 'BusinessUnit.name'),
            'order' => array('BusinessUnit.name' => 'ASC')
        ));

        return $data;
    }

    /**
     * Get data to set in the controller which relates to ajax request to calculate risk score.
     * This is mainly used by risk classifications while adding/editing an object.
     *    
     * @param  array  $classificationIds Risk Classification IDs
     * @param  array  $relatedItemIds    Parent object IDs which primarily relates to the current model
     * @return array                     Array of data
     */
    public function getRiskCalculationData($classificationIds, $relatedItemIds)
    {
        $calculationMethod = $this->Behaviors->RiskCalculationManager->getMethod($this);

        // risk score configuration
        $riskScore = $this->calculateRiskScore($classificationIds, $relatedItemIds);
        $riskCalculationMath = $this->getCalculationMath();
        $otherData = $this->getOtherData();
        $classificationCriteria = $this->RiskClassification->getRiskCriteria($classificationIds);

        // if magerit it calculation method, we take the highest classification out of one type
        // and then the other classification type as 2 classifications to check threshold from
        if ($calculationMethod == RiskCalculation::METHOD_MAGERIT) {
            $mageritSecondPartClassification = array_pop($classificationIds);

            $maxValue = $this->RiskClassification->find('first', [
                'conditions' => [
                    'RiskClassification.id' => $classificationIds
                ],
                'fields' => [
                    'MAX(RiskClassification.value) as max'
                ],
                'recursive' => -1
            ]);

            if ($maxValue[0]['max'] !== null) {
                $max = $this->RiskClassification->find('first', [
                    'conditions' => [
                        'RiskClassification.value' => $maxValue[0]['max'],
                        'RiskClassification.id' => $classificationIds
                    ],
                    'fields' => [
                        'RiskClassification.id'
                    ],
                    'recursive' => -1
                ]);
                
                $mageritFirstPartClassification = $max['RiskClassification']['id'];

                $classificationIds = [$mageritFirstPartClassification, $mageritSecondPartClassification];
            }
        }

        $appetiteThreshold = $this->RiskClassification->getRiskAppetiteThreshold($classificationIds);

        $setData = [
            'riskScore' => $riskScore,
            'riskAppetite' => Configure::read('Eramba.Settings.RISK_APPETITE'),
            'riskCalculationMath' => $riskCalculationMath,
            'otherData' => $otherData,
            'classificationCriteria' => $classificationCriteria,
            'riskAppetiteThreshold' => [
                'data' => $appetiteThreshold,
            ]
        ];

        return $setData;
    }

    public function getClassifications() {
        $classificationsRaw = $this->RiskClassification->find('all', array(
            'fields' => array('RiskClassification.id', 'RiskClassification.name'),
            'contain' => array(
                'RiskClassificationType' => array(
                    'fields' => array('RiskClassificationType.name')
                )
            )
        ));

        $classifications = array();
        
        foreach ($classificationsRaw as $item) {
            $classifications[$item['RiskClassification']['id']] = '[' . $item['RiskClassificationType']['name'] . '] ' . $item['RiskClassification']['name'];
        }

        return $classifications;
    }

    public function getExpiredReviewStatus() {
        $status = array(
            RISK_EXPIRED_REVIEWS => __('Yes'),
            RISK_NOT_EXPIRED_REVIEWS => __('No')
        );

        return $status;
    }

    public function getResidualScoreOptions() {
        $multiplier = 10;

        $percentages = array();
        for ( $i = 0; $i <= $multiplier; $i++ ) {
            $val = $i * 10;
            $percentages[$val] = CakeNumber::toPercentage($val, 0);
        }

        return array_reverse($percentages, true);
    }

    /**
     * Get the risk threshold data for a specific risk and specific classification type.
     */
    public function riskThreshold($riskId, $type = RiskClassification::TYPE_ANALYSIS) {
        $classificationIds = $this->getRelatedClassifications($riskId, $type);
        $threshold = ClassRegistry::init('RiskAppetiteThreshold')->getThreshold($classificationIds);

        return $threshold;
    }

    /**
     * Get classification IDs associated to a specific Risk.
     */
    public function getRelatedClassifications($riskId, $type = RiskClassification::TYPE_ANALYSIS) {
        $assoc = $this->getAssociated('RiskClassification');
        $with = $assoc['with'];
        $foreignKey = $assoc['foreignKey'];

        return $this->{$with}->find('list', [
            'conditions' => [
                $foreignKey => $riskId,
                'type' => $type
            ],
            'fields' => [
                'risk_classification_id'
            ],
            'recursive' => -1
        ]);
    }

    /**
     * Get the default form classifications that are supposed to be used for form rendering
     * and validation.
     */
    public function getDefaultFormClassifications()
    {
        $calculationValues = $this->getClassificationTypeValues($this->getSectionValues());
        $classifications = $this->RiskClassification->RiskClassificationType->find('all', array(
            'conditions' => array(
                'RiskClassificationType.id' => $calculationValues
            ),
            'order' => array('RiskClassificationType.name' => 'ASC'),
            'recursive' => 1
        ));

        return $classifications;
    }

    public function convertTagsImport($value) {
        if (!empty($value)) {
            return $value;
        }

        return false;
    }

    public function getThresholds() {
        $RiskAppetiteThreshold = ClassRegistry::init('RiskAppetiteThreshold');

        return $RiskAppetiteThreshold->getList();
    }

    public function hasSectionIndex()
    {
        return true;
    }
}
