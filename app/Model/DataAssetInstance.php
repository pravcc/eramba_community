<?php
App::uses('AppModel', 'Model');
App::uses('DataAsset', 'Model');
App::uses('DataAssetSetting', 'Model');
App::uses('Hash', 'Utility');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('Country', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');

class DataAssetInstance extends AppModel implements InheritanceInterface
{
    public $displayField = null;

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

    public $actsAs = [
        'Containable',
        'Search.Searchable',
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report',
            ]
        ],
        'ObjectStatus.ObjectStatus',
        'Visualisation.Visualisation',
        'CustomRoles.CustomRoles',
        'Comments.Comments',
        'Attachments.Attachments',
        'Widget.Widget',
        'UserFields.UserFields',
        'Macros.Macro',
        'SubSection' => [
            'childModels' => true
        ],
        'AdvancedFilters.AdvancedFilters'
    ];

    public $validate = [
    ];

    public $belongsTo = [
        'Asset'
    ];

    public $hasOne = [
        'DataAssetSetting'
    ];

    public $hasMany = [
        'DataAsset',
    ];

    public $hasAndBelongsToMany = [
    ];

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function analysisStatuses($value = null)
    {
        $options = [
            self::ANALYSIS_STATUS_LOCKED => __('Locked'),
            self::ANALYSIS_STATUS_UNLOCKED => __('Unlocked'),
        ];
        return parent::enum($value, $options);
    }

    const ANALYSIS_STATUS_LOCKED = 0;
    const ANALYSIS_STATUS_UNLOCKED = 1;

    public function __construct($id = false, $table = null, $ds = null)
    {
        //
        // Init helper Lib for UserFields Module
        $UserFields = new UserFields();
        //

        $this->label = __('Data Assets');
        $this->_group = parent::SECTION_GROUP_ASSET_MGT;

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'asset_id' => [
                'label' => __('Asset'),
                'editable' => false,
            ],
            'analysis_unlocked' => [
                'label' => __('Analysis Unlocked'),
                'editable' => false,
                'options' => [$this, 'analysisStatuses']
            ],
            'DataAsset' => [
                'label' => __('Data Assets'),
                'editable' => false,
            ],
            'incomplete_gdpr_analysis' => [
                'label' => __('Incomplete GDPR Analysis'),
                'type' => 'toggle',
                'editable' => false,
                'hidden' => true,
            ],
        ];

        $this->advancedFilterSettings = array(
            'pdf_title' => __('Data Asset Instances'),
            'pdf_file_name' => __('data_asset_instances'),
            'csv_file_name' => __('data_asset_instances'),
            'bulk_actions' => true,
            'history' => true,
            'trash' => true,
            'use_new_filters' => true,
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
                ->multipleSelectField('asset_id', [ClassRegistry::init('Asset'), 'getList'], [
                    'showDefault' => true
                ])
                ->userField('Asset-AssetOwner', 'AssetOwner', [
                    'findField' => 'Asset.UserFieldsObjectAssetOwner.object_key',
                    'label' => __('Asset Owner'),
                    'showDefault' => true
                ])
                ->objectStatusField('ObjectStatus_asset_missing_review', 'asset_missing_review')
                ->objectStatusField('ObjectStatus_incomplete_analysis', 'incomplete_analysis')
            ->group('DataAssetSetting', [
                'name' => __('GDPR')
            ])
                ->selectField('DataAssetSetting-gdpr_enabled', [$this, 'getStatusFilterOption'], [
                    'showDefault' => true
                ])
                ->textField('DataAssetSetting-driver_for_compliance', [
                    'showDefault' => true
                ])
                ->multipleSelectField('DataAssetSetting-Dpo', [$this, 'getUsers'], [
                    'showDefault' => true
                ])
                ->multipleSelectField('DataAssetSetting-Processor', [ClassRegistry::init('ThirdParty'), 'getList'], [
                    'showDefault' => true
                ])
                ->multipleSelectField('DataAssetSetting-Controller', [ClassRegistry::init('ThirdParty'), 'getList'], [
                    'showDefault' => true
                ])
                ->multipleSelectField('DataAssetSetting-ControllerRepresentative', [$this, 'getUsers'], [
                    'showDefault' => true
                ])
                ->multipleSelectField('SupervisoryAuthority-country_id', [ClassRegistry::init('Country'), 'europeCountries'], [
                    'label' => __('Supervisory Authority'),
                    'findField' => 'DataAssetSetting.SupervisoryAuthority.country_id',
                    'fieldData' => 'DataAssetSetting.SupervisoryAuthority',
                    'showDefault' => true
                ])
                ->objectStatusField('ObjectStatus_incomplete_gdpr_analysis', 'incomplete_gdpr_analysis')
            ->group('Risk', [
                'name' => __('Asset Risk')
            ])
                ->multipleSelectField('DataAsset-Risk', [ClassRegistry::init('Risk'), 'getList'], [
                    'label' => __('Asset Risk')
                ])
                ->objectStatusField('ObjectStatus_risk_expired_reviews', 'risk_expired_reviews')
            ->group('ThirdPartyRisk', [
                'name' => __('Third Party Risk')
            ])
                ->multipleSelectField('DataAsset-ThirdPartyRisk', [ClassRegistry::init('ThirdPartyRisk'), 'getList'], [
                    'label' => __('Third Party Risk')
                ])
                ->objectStatusField('ObjectStatus_third_party_risk_expired_reviews', 'third_party_risk_expired_reviews')
            ->group('BusinessContinuity', [
                'name' => __('Business Impact Analysis')
            ])
                ->multipleSelectField('DataAsset-BusinessContinuity', [ClassRegistry::init('BusinessContinuity'), 'getList'], [
                    'label' => __('Business Impact Analysis')
                ])
                ->objectStatusField('ObjectStatus_business_continuity_expired_reviews', 'business_continuity_expired_reviews')
            ->group('SecurityService', [
                'name' => __('Internal Control')
            ])
                ->multipleSelectField('DataAsset-SecurityService', [ClassRegistry::init('SecurityService'), 'getList'], [
                    'label' => __('Internal Control'),
                ])
                ->objectStatusField('ObjectStatus_controls_with_issues', 'controls_with_issues')
                ->objectStatusField('ObjectStatus_controls_with_failed_audits', 'controls_with_failed_audits')
                ->objectStatusField('ObjectStatus_controls_with_missing_audits', 'controls_with_missing_audits')
            ->group('SecurityPolicy', [
                'name' => __('Security Policy')
            ])
                ->multipleSelectField('DataAsset-SecurityPolicy', [ClassRegistry::init('SecurityPolicy'), 'getList'], [
                    'label' => __('Security Policy'),
                ])
                ->objectStatusField('ObjectStatus_policies_with_missing_reviews', 'policies_with_missing_reviews')
            ->group('Project', [
                'name' => __('Project Management')
            ])
                ->multipleSelectField('DataAsset-Project', [ClassRegistry::init('Project'), 'getList'], [
                    'label' => __('Project')
                ])
                ->textField('Project-ProjectAchievement', [
                    'label' => __('Project Task'),
                    'findField' => 'DataAsset.Project.ProjectAchievement.description',
                    'fieldData' => 'DataAsset.Project.ProjectAchievement'
                ])
                ->objectStatusField('ObjectStatus_project_planned', 'project_planned')
                ->objectStatusField('ObjectStatus_project_ongoing', 'project_ongoing')
                ->objectStatusField('ObjectStatus_project_closed', 'project_closed')
                ->objectStatusField('ObjectStatus_project_expired', 'project_expired')
                ->objectStatusField('ObjectStatus_project_expired_tasks', 'project_expired_tasks');

        $this->otherFilters($advancedFilterConfig);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function getDisplayFilterFields()
    {
        return ['asset_id'];
    }

    public function getReportsConfig()
    {
        return [
            'finder' => [
                'options' => [
                    'contain' => [
                        'Asset' => [
                            'AssetMediaType',
                            'AssetLabel',
                            'DataAssetInstance',
                            'AssetReview',
                            'Review',
                            'CustomFieldValue',
                            'RelatedAssets',
                            'BusinessUnit',
                            'Legal',
                            'AssetClassification' => [
                                'AssetClassificationType'
                            ],
                            'Risk',
                            'ThirdPartyRisk',
                            'SecurityIncident',
                            'ComplianceManagement',
                            'AssetOwner',
                            'AssetOwnerGroup',
                            'AssetGuardian',
                            'AssetGuardianGroup',
                            'AssetUser',
                            'AssetUserGroup'
                        ],
                        'DataAssetSetting' => [
                            'DataAssetInstance',
                            'SupervisoryAuthority',
                            'Dpo',
                            'Processor',
                            'Controller',
                            'ControllerRepresentative',
                            'DataOwner',
                            'DataOwnerGroup'
                        ],
                        'DataAsset' => [
                            'DataAssetStatus',
                            'DataAssetInstance',
                            'DataAssetGdpr',
                            'CustomFieldValue',
                            'Project',
                            'Risk',
                            'ThirdPartyRisk',
                            'BusinessContinuity',
                            'SecurityService',
                            'BusinessUnit',
                            'ThirdParty',
                            'SecurityPolicy'
                        ]
                    ]
                ]
            ],
            'table' => [
                'model' => [
                    'Asset', 'DataAssetSetting', 'DataAsset'
                ]
            ],
            'chart' => [
                1 => [
                    'title' => __('Data Flow Tree'),
                    'description' => __('This tree chart shows for a given asset all its stages, flows and mitigation controls, policies, risks and projects.'),
                    'type' => ReportBlockChartSetting::TYPE_TREE,
                    'templateType' => ReportTemplate::TYPE_ITEM,
                    'dataFn' => 'dataFlowTreeChart'
                ],
            ]
        ];
    }

    public function getSectionInfoConfig()
    {
        return [
            'map' => [
                'DataAsset' => [
                    'ThirdParty',
                    'BusinessUnit',
                    'Risk',
                    'ThirdPartyRisk',
                    'BusinessContinuity',
                    'SecurityService' => [
                        'SecurityServiceAudit',
                        'SecurityServiceIssue',
                        'SecurityServiceMaintenance',
                    ],
                    'Project' => [
                        'ProjectAchievement',
                    ],  
                    'SecurityPolicy'
                ],
            ]
        ];
    }

    public function getMacrosConfig()
    {
        return [
            'assoc' => [
                'Asset', 'DataAssetSetting',
            ],
        ];
    }

    public function parentModel() {
        return 'Asset';
    }

    public function parentNode($type) {
        return $this->visualisationParentNode('asset_id');
    }

    public function getItem($id) {
        $data = $this->find('first', [
            'conditions' => [
                $this->alias . '.id' => $id
            ],
            'recursive' => 1
        ]);

        if (empty($data)) {
            throw new NotFoundException();
        }

        return $data;
    }

    public function unlockAnalysis($id) {
        $ret = $this->updateAll(['analysis_unlocked' => self::ANALYSIS_STATUS_UNLOCKED], [
            'DataAssetInstance.id' => $id
        ]);

        return $ret;
    }

    public function getObjectStatusConfig() {
        return [
            'asset_missing_review' => [//inherited
                'title' => __('Asset Review Expired'),
                'inherited' => [
                    'Asset' => 'expired_reviews'
                ],
                'regularTrigger' => true,
            ],
            'controls_with_issues' => [//inherited
                'title' => __('Controls with Issues'),
                'inherited' => [
                    'DataAsset.SecurityService' => 'control_with_issues'
                ],
                'type' => 'danger',
            ],
            'controls_with_failed_audits' => [//inherited
                'title' => __('Control Audit Failed'),
                'inherited' => [
                    'DataAsset.SecurityService' => 'audits_last_not_passed'
                ],
                'type' => 'danger',
                'regularTrigger' => true,
            ],
            'controls_with_missing_audits' => [//inherited
                'title' => __('Control Audit Expired'),
                'inherited' => [
                    'DataAsset.SecurityService' => 'audits_last_missing'
                ],
                'regularTrigger' => true,
            ],
            'policies_with_missing_reviews' => [//inherited
                'title' => __('Policy Review Expired'),
                'inherited' => [
                    'DataAsset.SecurityPolicy' => 'expired_reviews'
                ],
                'regularTrigger' => true,
            ],
            // 'risks_with_missing_reviews' => [//inherited
            //     'title' => __('Risks Review Expired'),
            //     'inherited' => [
            //         'DataAsset.Risk' => 'expired_reviews',
            //         'DataAsset.ThirdPartyRisk' => 'expired_reviews',
            //         'DataAsset.BusinessContinuity' => 'expired_reviews',
            //     ],
            // ],
            'risk_expired_reviews' => [//inherited
                'title' => __('Asset Risk Review Expired'),
                'inherited' => [
                    'DataAsset.Risk' => 'expired_reviews',
                ],
                'regularTrigger' => true,
            ],
            'third_party_risk_expired_reviews' => [//inherited
                'title' => __('Third Party Risk Review Expired'),
                'inherited' => [
                    'DataAsset.ThirdPartyRisk' => 'expired_reviews',
                ],
                'regularTrigger' => true,
            ],
            'business_continuity_expired_reviews' => [//inherited
                'title' => __('Business Risk Review Expired'),
                'inherited' => [
                    'DataAsset.BusinessContinuity' => 'expired_reviews',
                ],
                'regularTrigger' => true,
            ],
            'incomplete_analysis' => [
                'title' => __('Incomplete Analysis'),
                'callback' => [$this, 'statusIncompleteAnalysis'],
            ],
            'incomplete_gdpr_analysis' => [
                'title' => __('Incomplete GDPR Analysis'),
                'callback' => [$this, 'statusIncompleteGdprAnalysis'],
            ],
            // 'project_expired' => [
            //     'title' => __('Project Expired'),
            //     'inherited' => [
            //         'DataAsset.Project' => 'expired'
            //     ],
            // ],
            // 'expired_tasks' => [
            //     'title' => __('Project Task Expired'),
            //     'inherited' => [
            //         'DataAsset.Project' => 'expired_tasks'
            //     ],
            // ],
            'project_expired' => [
                'title' => __('Project Expired'),
                'inherited' => [
                    'DataAsset.Project' => 'expired'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'project_expired_tasks' => [
                'title' => __('Project Task Expired'),
                'inherited' => [
                    'DataAsset.Project' => 'expired_tasks'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'project_ongoing' => [
                'title' => __('Project Ongoing'),
                'inherited' => [
                    'DataAsset.Project' => 'ongoing'
                ],
                'type' => 'success',
                'storageSelf' => false
            ],
            'project_planned' => [
                'title' => __('Project Planned'),
                'inherited' => [
                    'DataAsset.Project' => 'planned'
                ],
                'type' => 'success',
                'storageSelf' => false
            ],
            'project_closed' => [
                'title' => __('Project Closed'),
                'inherited' => [
                    'DataAsset.Project' => 'closed'
                ],
                'type' => 'success',
                'storageSelf' => false
            ],
            'project_no_updates' => [
                'title' => __('Project Missing Updates'),
                'inherited' => [
                    'DataAsset.Project' => 'no_updates'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusIncompleteAnalysis() {
        $data = $this->find('count', [
            'conditions' => [
                'DataAssetInstance.id' => $this->id
            ],
            'joins' => [
                [
                    'table' => 'data_asset_settings',
                    'alias' => 'DataAssetSetting',
                    'type' => 'INNER',
                    'conditions' => [
                        'DataAssetSetting.data_asset_instance_id = DataAssetInstance.id',
                        'DataAssetSetting.gdpr_enabled' => DataAssetSetting::GDPR_DISABLED,
                    ]
                ],
                [
                    'table' => 'data_assets',
                    'alias' => 'DataAsset',
                    'type' => 'LEFT',
                    'conditions' => [
                        'DataAsset.deleted = 0',
                        'DataAsset.data_asset_instance_id = DataAssetInstance.id',
                    ]
                ],
            ],
            'group' => [
                'DataAsset.data_asset_status_id'
            ],
            'recursive' => -1
        ]);

        return (boolean) (is_numeric($data) && $data != count(DataAsset::statuses()));
    }

    public function statusIncompleteGdprAnalysis() {
        $data = $this->find('count', [
            'conditions' => [
                'DataAssetInstance.id' => $this->id
            ],
            'joins' => [
                [
                    'table' => 'data_asset_settings',
                    'alias' => 'DataAssetSetting',
                    'type' => 'INNER',
                    'conditions' => [
                        'DataAssetSetting.data_asset_instance_id = DataAssetInstance.id',
                        'DataAssetSetting.gdpr_enabled' => DataAssetSetting::GDPR_ENABLED,
                    ]
                ],
                [
                    'table' => 'data_assets',
                    'alias' => 'DataAsset',
                    'type' => 'LEFT',
                    'conditions' => [
                        'DataAsset.deleted = 0',
                        'DataAsset.data_asset_instance_id = DataAssetInstance.id',
                    ]
                ],
            ],
            'group' => [
                'DataAsset.data_asset_status_id'
            ],
            'recursive' => -1
        ]);

        return (boolean) (is_numeric($data) && $data != count(DataAsset::statuses()));
    }

    public function getAssets()
    {
        return $this->Asset->getList();
    }

    public function getBusinessUnits()
    {
        return ClassRegistry::init('BusinessUnit')->getList();
    }

    public function getThirdParties() {
        return ClassRegistry::init('ThirdParty')->getList();
    }

    public function getEuropeCountries() {
        return Country::europeCountries();
    }

    public function hasSectionIndex()
    {
        return true;
    }

}
