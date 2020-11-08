<?php
App::uses('AppMonel', 'Model');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('DataAssetSettingsUser', 'Model');
App::uses('Country', 'Model');
App::uses('DataAssetSettingsThirdParty', 'Model');
App::uses('DataAssetGdprDataType', 'Model');
App::uses('DataAssetGdprCollectionMethod', 'Model');
App::uses('DataAssetGdprLawfulBase', 'Model');
App::uses('DataAssetGdprThirdPartyCountry', 'Model');
App::uses('DataAssetGdprArchivingDriver', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('UserFields', 'UserFields.Lib');

class DataAsset extends AppModel implements InheritanceInterface
{
	public $displayField = 'title';

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

	public $actsAs = [
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => [
			'config' => 'Strict',
			'fields' => [
				'description', 'data_asset_status_id', 'asset_id'
			]
		],
        'ModuleDispatcher' => [
            'behaviors' => [
                'CustomFields.CustomFields',
                'Reports.Report',
            ]
        ],
        'AuditLog.Auditable',
        'Utils.SoftDelete',
        'ObjectStatus.ObjectStatus',
        'Visualisation.Visualisation',
        'UserFields.UserFields',
        'Comments.Comments',
        'Attachments.Attachments',
        'Widget.Widget',
        'Macros.Macro',
        'SubSection' => [
            'parentField' => 'data_asset_instance_id'
        ],
        'AdvancedFilters.AdvancedFilters'
	];

	public $mapping = [
		'titleColumn' => 'description',
		'logRecords' => true,
		'workflow' => false
	];

	public $workflow = [
	];

	public $validate = [
		'data_asset_status_id' => [
			'rule' => 'notBlank',
			'required' => true
		],
		'title' => [
			'rule' => 'notBlank',
			'required' => true
		],
	];

	public $gdprValidate = [
        'BusinessUnit' => [
            'minCount' => [
                'rule' => ['multiple', ['min' => 1]],
                'message' => 'You have to select at least one option',
                'required' => true
            ]
        ],
	];

	public $belongsTo = [
		'DataAssetStatus',
		'DataAssetInstance'
	];

    public $hasOne = [
        'DataAssetGdpr'
    ];

	public $hasMany = [
	];

	public $hasAndBelongsToMany = [
		'SecurityService',
		'BusinessUnit',
		'ThirdParty',
		'Project' => [
			'with' => 'DataAssetsProject'
		],
		'SecurityPolicy',
		'Risk' => [
            'className' => 'Risk',
            'with' => 'DataAssetsRisk',
            'joinTable' => 'data_assets_risks',
            'foreignKey' => 'data_asset_id',
            'associationForeignKey' => 'risk_id',
            'conditions' => [
                'DataAssetsRisk.model' => 'Risk'
            ]
        ],
        'ThirdPartyRisk' => [
            'className' => 'ThirdPartyRisk',
            'with' => 'DataAssetsRisk',
            'joinTable' => 'data_assets_risks',
            'foreignKey' => 'data_asset_id',
            'associationForeignKey' => 'risk_id',
            'conditions' => [
                'DataAssetsRisk.model' => 'ThirdPartyRisk'
            ]
        ],
        'BusinessContinuity' => [
            'className' => 'BusinessContinuity',
            'with' => 'DataAssetsRisk',
            'joinTable' => 'data_assets_risks',
            'foreignKey' => 'data_asset_id',
            'associationForeignKey' => 'risk_id',
            'conditions' => [
                'DataAssetsRisk.model' => 'BusinessContinuity'
            ]
        ],
	];

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = [
            self::STATUS_COLLECTED => __('Collected'),
            self::STATUS_MODIFIED => __('Modified'),
            self::STATUS_STORED => __('Stored'),
            self::STATUS_TRANSIT => __('Transit'),
            self::STATUS_DELETED => __('Deleted'),
        ];
        return parent::enum($value, $options);
    }

    const STATUS_COLLECTED = 1;
    const STATUS_MODIFIED = 2;
    const STATUS_STORED = 3;
    const STATUS_TRANSIT = 4;
    const STATUS_DELETED = 5;

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function statusesInfo($value = null) {
        $options = [
        	self::STATUS_COLLECTED => __('When information is created, for example a receptionist takes note of a customer patient data for an appointment or a system receives credit card information to process a payment.'),
            self::STATUS_MODIFIED => __('When existing records are modified, for example a medical appointment is updated or contact information is updated over the phone.'),
            self::STATUS_STORED => __('Data is stored in paper or digital format.'),
            self::STATUS_TRANSIT => __('Data is sent over networks, voice (telephone).'),
            self::STATUS_DELETED => __('When data is deleted, for example when malfunction hard drives are destroyed, a system deletes with SQL commands, a share drive file is "Trashed".'),
        ];
        return parent::enum($value, $options);
    }

	public function __construct($id = false, $table = null, $ds = null)
    {
        //
        // Init helper Lib for UserFields Module
        $UserFields = new UserFields();
        //
        
		$this->label = __('Data Asset Flow');
        $this->_group = parent::SECTION_GROUP_ASSET_MGT;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'risk-management' => [
				'label' => __('Risk Management')
			],
			'mitigating-controls' => [
				'label' => __('Mitigating Controls')
			],
            'gdpr' => [
                'label' => __('GDPR')
            ]
		];

		$this->fieldData = [
            'data_asset_instance_id' => [
                'label' => __('Data Asset'),
                'editable' => true,
                'empty' => __('Choose one ...'),
                'renderHelper' => ['DataAssets', 'dataAssetInstanceField'],
                'options' => [$this, 'getDataAssetInstances'],
            ],
            'data_asset_status_id' => [
                'label' => __('Stage'),
                'editable' => true,
                'options' => [$this, 'statuses'],
                'description' => __('Choose the stage being analysed.'),
                'renderHelper' => ['DataAssets', 'dataAssetStatusIdField']
            ],
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'description' => __('Set a title that describes this flow and stage. For example "Callcenter receives customer information".'),
			],
			'description' => [
				'label' => __('Flow Description'),
				'editable' => true,
				'description' => __('OPTIONAL: Describe the stage, the context of the situation, Etc.'),
			],
			'BusinessUnit' => [
				'label' => __('Business Units'),
				'editable' => true,
				'description' => __('Select one or more Business Units (Organisation / Business Units) involved on this stage.'),
                'quickAdd' => true,
			],
			'ThirdParty' => [
				'label' => __('Third Parties'),
				'editable' => true,
				'description' => __('OPTIONAL: Select one ore more Third Parties (Organisation / Third Parties) with whom this information is being shared. Sharing data with Third Parties is an indication that further risk analysis must be involved.'),
                'quickAdd' => true,
			],
            'order' => [
                'label' => __('Order'),
                'editable' => true,
                'type' => 'select',
                'description' => __('Select from the dropbox the "place" in which this stage happens.'),
            ],
			'Risk' => [
				'label' => __('Asset Risks'),
				'editable' => true,
				'group' => 'risk-management',
				'description' => __('Select one or more Risks from the Risk Management / Asset Risk Management module that are related to this stage. All controls and policies used in this Risk will be pre-selected on the next tab "Mitigation Controls".'),
                'renderHelper' => ['DataAssets', 'riskField'],
                'quickAdd' => true,
			],
			'ThirdPartyRisk' => [
				'label' => __('Third Party Risks'),
				'editable' => true,
				'group' => 'risk-management',
				'description' => __('Select one or more Risks from the Risk Management / Third Party Risk Management module that are related to this stage.'),
                'renderHelper' => ['DataAssets', 'thirdPartyRiskField'],
                'quickAdd' => true,
			],
			'BusinessContinuity' => [
				'label' => __('Business Continuities'),
				'editable' => true,
				'group' => 'risk-management',
				'description' => __('Select one or more Risks from the Risk Management / Business Impact Analysis module that are related to this stage.'),
                'renderHelper' => ['DataAssets', 'businessContinuityField'],
                'quickAdd' => true,
			],
            'SecurityService' => [
                'label' => __('Compensating controls'),
                'editable' => true,
                'group' => 'mitigating-controls',
                'description' => __('Choose one or more controls from Control Catalogue / Security Services module used to protect this asset in this particular stage.'),
                'options' => [$this, 'getNotDesignSecurityServices'],
                'renderHelper' => ['DataAssets', 'securityServiceField'],
                'quickAdd' => true,
            ],
            'Project' => [
                'label' => __('Improvement Projects'),
                'editable' => true,
                'group' => 'mitigating-controls',
                'description' => __('Choose one or more projects from Security Operations / Project Management that describe the improvements planned for this stage.'),
                'renderHelper' => ['DataAssets', 'projectField'],
                'quickAdd' => true,
            ],
			'SecurityPolicy' => [
				'label' => __('Security Policies'),
				'editable' => true,
				'options' => [$this, 'getSecurityPolicies'],
				'group' => 'mitigating-controls',
				'description' => __('Select one or more documents (Control Catalogue / Security Policies) that describe related policies, procedures, templates and standards for this stage.'),
                'renderHelper' => ['DataAssets', 'securityPolicyField'],
                'quickAdd' => true,
			],
		];

        $this->advancedFilterSettings = [
            'pdf_title' => __('Data Asset Flows'),
            'pdf_file_name' => __('data_asset_flows'),
            'csv_file_name' => __('data_asset_flows'),
            'max_selection_size' => 30,
            'bulk_actions' => true,
            'url' => [
                'controller' => 'dataAssets',
                'action' => 'index',
                '?' => [
                    'advanced_filter' => 1
                ]
            ],
            'reset' => [
                'controller' => 'dataAssetInstances',
                'action' => 'index',
            ],
            'history' => true,
            'trash' => [
                'controller' => 'dataAssets',
                'action' => 'trash',
                '?' => [
                    'advanced_filter' => 1
                ]
            ],
            'use_new_filters' => true,
            'scrollable_tabs' => true
        ];

		parent::__construct($id, $table, $ds);
	}

    public function getAdvancedFilterConfig()
    {
        $advancedFilterConfig = $this->createAdvancedFilterConfig()
            ->group('general', [
                'name' => __('General')
            ])
                ->nonFilterableField('id')
                ->multipleSelectField('DataAssetInstance-asset_id', [ClassRegistry::init('Asset'), 'getList'])
                ->multipleSelectField('data_asset_status_id', [$this, 'statuses'], [
                    'showDefault' => true
                ])
                ->textField('title', [
                    'showDefault' => true
                ])
                ->textField('description')
                ->numberField('order')
                ->multipleSelectField('BusinessUnit', [ClassRegistry::init('BusinessUnit'), 'getList'])
                ->multipleSelectField('ThirdParty', [ClassRegistry::init('ThirdParty'), 'getList']);

        $this->Risk->relatedFilters($advancedFilterConfig);
        $this->ThirdPartyRisk->relatedFilters($advancedFilterConfig);
        $this->BusinessContinuity->relatedFilters($advancedFilterConfig);
        $this->Project->relatedFilters($advancedFilterConfig);
        $this->SecurityService->relatedFilters($advancedFilterConfig);
        $this->SecurityPolicy->relatedFilters($advancedFilterConfig);

        $advancedFilterConfig
            ->group('collected', [
                'name' => __('Collected')
            ])
                ->multipleSelectField('DataAssetGdprDataType-data_type', ['DataAssetGdprDataType', 'dataTypes'], [
                    'findField' => 'DataAssetGdpr.DataAssetGdprDataType.data_type',
                    'fieldData' => 'DataAssetGdpr.DataAssetGdprDataType.data_type'
                ])
                ->textField('DataAssetGdpr-purpose')
                ->textField('DataAssetGdpr-right_to_be_informed')
                ->textField('DataAssetGdpr-data_subject')
                ->multipleSelectField('DataAssetGdprCollectionMethod-collection_method', ['DataAssetGdprCollectionMethod', 'collectionMethods'], [
                    'findField' => 'DataAssetGdpr.DataAssetGdprCollectionMethod.collection_method',
                    'fieldData' => 'DataAssetGdpr.DataAssetGdprCollectionMethod.collection_method'
                ])
                ->textField('DataAssetGdpr-volume')
                ->textField('DataAssetGdpr-recived_data', [
                    'label' => __('Recived Data'),
                ])
                ->multipleSelectField('DataAssetGdprLawfulBase-lawful_base', ['DataAssetGdprLawfulBase', 'lawfulBases'], [
                    'findField' => 'DataAssetGdpr.DataAssetGdprLawfulBase.lawful_base',
                    'fieldData' => 'DataAssetGdpr.DataAssetGdprLawfulBase.lawful_base'
                ])
                ->textField('DataAssetGdpr-contracts', [
                    'label' => __('Applicable Contracts, Code of Conducts and Privacy Notes'),
                ])
            ->group('modified', [
                'name' => __('Modified')
            ])
                ->textField('DataAssetGdpr-stakeholders')
                ->textField('DataAssetGdpr-accuracy')
                ->textField('DataAssetGdpr-right_to_access')
                ->textField('DataAssetGdpr-right_to_rectification')
                ->textField('DataAssetGdpr-right_to_decision')
                ->textField('DataAssetGdpr-right_to_object')
            ->group('stored', [
                'name' => __('Stored')
            ])
                ->textField('DataAssetGdpr-retention')
                ->textField('DataAssetGdpr-encryption')
                ->textField('DataAssetGdpr-right_to_erasure')
                ->multipleSelectField('DataAssetGdprArchivingDriver-archiving_driver', ['DataAssetGdprArchivingDriver', 'archivingDrivers'], [
                    'findField' => 'DataAssetGdpr.DataAssetGdprArchivingDriver.archiving_driver',
                    'fieldData' => 'DataAssetGdpr.DataAssetGdprArchivingDriver.archiving_driver'
                ])
            ->group('transit', [
                'name' => __('Transit')
            ])
                ->textField('DataAssetGdpr-origin')
                ->textField('DataAssetGdpr-destination')
                ->selectField('DataAssetGdpr-transfer_outside_eea', [$this, 'getStatusFilterOption'], [
                    'label' => __('Data Transfers outside the EEA'),
                ])
                ->multipleSelectField('ThirdPartyInvolved-country_id', ['Country', 'countries'], [
                    'label' => __('Third Party Countries Involved'),
                    'findField' => 'DataAssetGdpr.ThirdPartyInvolved.country_id',
                    'fieldData' => 'DataAssetGdpr.ThirdPartyInvolved.country_id'
                ])
                ->selectField('DataAssetGdpr-third_party_involved_all', [$this, 'getStatusFilterOption'], [
                    'label' => __('Anywhere in the world'),
                ])
                ->multipleSelectField('DataAssetGdprThirdPartyCountry-third_party_country', ['DataAssetGdprThirdPartyCountry', 'thirdPartyCountries'], [
                    'label' => __('Third Party Countries Involved'),
                    'findField' => 'DataAssetGdpr.DataAssetGdprThirdPartyCountry.third_party_country',
                    'fieldData' => 'DataAssetGdpr.DataAssetGdprThirdPartyCountry.third_party_country'
                ])
                ->textField('DataAssetGdpr-security')
                ->textField('DataAssetGdpr-right_to_portability');

        if (AppModule::loaded('CustomFields')) {
            $this->customFieldsFilters($advancedFilterConfig);
        }

        $this->otherFilters($advancedFilterConfig);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function getObjectStatusConfig() {
        return [
            'asset_missing_review' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'controls_with_issues' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'controls_with_failed_audits' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'controls_with_missing_audits' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'policies_with_missing_reviews' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'risks_with_missing_reviews' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'incomplete_analysis' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'incomplete_gdpr_analysis' => [
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'hidden' => true
            ],
            'project_expired' => [
                'title' => __('Project Expired'),
                'inherited' => [
                    'Project' => 'expired'
                ],
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'project_expired_tasks' => [
                'title' => __('Project Task Expired'),
                'inherited' => [
                    'Project' => 'expired_tasks'
                ],
                'trigger' => [
                    $this->DataAssetInstance
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
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'storageSelf' => false
            ],
            'project_planned' => [
                'title' => __('Project Planned'),
                'inherited' => [
                    'Project' => 'planned'
                ],
                'type' => 'success',
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'storageSelf' => false
            ],
            'project_closed' => [
                'title' => __('Project Closed'),
                'inherited' => [
                    'Project' => 'closed'
                ],
                'type' => 'success',
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'storageSelf' => false
            ],
            'project_no_updates' => [
                'title' => __('Project Missing Updates'),
                'inherited' => [
                    'Project' => 'no_updates'
                ],
                'trigger' => [
                    $this->DataAssetInstance
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
        ];
    }

    public function getReportsConfig()
    {
        return [
            'finder' => [
                'options' => [
                    'contain' => [
                        'DataAssetStatus',
                        'DataAssetInstance' => [
                            'Asset',
                            'DataAssetSetting',
                        ],
                        'DataAssetGdpr' => [
                            'DataAsset',
                            'ThirdPartyInvolved',
                            'DataAssetGdprDataType',
                            'DataAssetGdprCollectionMethod',
                            'DataAssetGdprLawfulBase',
                            'DataAssetGdprThirdPartyCountry',
                            'DataAssetGdprArchivingDriver'
                        ],
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
            ],
            'table' => [
                'model' => [
                    'DataAssetGdpr'
                ]
            ],
        ];
    }

    public function getMacrosConfig()
    {
        return [
            'assoc' => [
                'DataAssetGdpr',
            ],
            'seed' => [
            ],
        ];
    }

    public function parentModel() {
        return 'DataAssetInstance';
    }

    public function parentNode($type) {
        return $this->visualisationParentNode('data_asset_instance_id');
    }

	public function enableGdprValidation() {
		$this->validate = am($this->validate, $this->gdprValidate);
	}

    public function beforeValidate($options = array()) {
        $ret = true;

        $gdprEnabled = (isset($this->data['DataAsset']['data_asset_instance_id']) && $this->isGdprEnabled($this->data['DataAsset']['data_asset_instance_id'])) ? true : false;

        // if ($gdprEnabled) {
        //     $ret &= $this->validateMultipleFields(['Risk', 'ThirdPartyRisk', 'BusinessContinuity'], __('Please choose at least one Asset Risk, Third Party Risk or Business Continuity.'), true);
        // }
        
        return true;
    }

    public function beforeSave($options = []) {
        // $this->transformDataToHabtm(['SecurityService', 'BusinessUnit', 'ThirdParty', 'Project',
        //     'Risk', 'ThirdPartyRisk', 'BusinessContinuity', 'SecurityPolicy'
        // ]);

        // $this->setHabtmConditionsToData(['Risk', 'ThirdPartyRisk', 'BusinessContinuity']);

        return true;
    }

	public function afterSave($created, $options = []) {
		if (!empty($this->id)) {
            $dataAssetInstanceId = $this->field('data_asset_instance_id', [
                'DataAsset.id' => $this->id
            ]);

			$this->updateOrder($this->id, $dataAssetInstanceId);
		}
    }

    public function getOrderOptions($dataAssetInstanceId, $dataAssetId = null)
    {
        $data = $this->find('all', [
            'conditions' => [
                'DataAsset.data_asset_instance_id' => $dataAssetInstanceId,
                'DataAsset.id !=' => $dataAssetId
            ],
            'order' => ['DataAsset.order' => 'ASC'],
            'contain' => ['DataAssetStatus'],
            'recursive' => -1
        ]);

        $order = [
            0 => __('1. Set this stage as the first one.')
        ];

        foreach ($data as $item) {
            $order[$item['DataAsset']['order'] + 1] = __('%s. put item after %s (%s)', $item['DataAsset']['order'] + 2, $item['DataAsset']['title'], $item['DataAssetStatus']['name']);
        }

        return $order;
    }

    private function updateOrder($id, $dataAssetInstanceId = null) {
        if ($dataAssetInstanceId === null) {
            $dataAssetInstanceId = $this->field('data_asset_instance_id', [
                'DataAsset.id' => $id
            ]);
        }

    	$dataAssets = $this->find('all', [
    		'conditions' => [
    			'DataAsset.data_asset_instance_id' => $dataAssetInstanceId 
    		],
    		'order' => [
    			'DataAsset.order' => 'ASC',
    			'DataAsset.modified' => 'DESC'
    		],
    		'recursive' => -1
    	]);

    	foreach ($dataAssets as $key => $item) {
    		$this->updateAll(['order' => $key], [
    			'DataAsset.id' => $item['DataAsset']['id']
			]);
    	}
    }

    public function getDataAssetInstances() {
        $data = $this->DataAssetInstance->DataAssetSetting->find('list', [
            'fields' => [
                'DataAssetSetting.data_asset_instance_id',
                'DataAssetSetting.name'
            ],
            'recursive' => -1
        ]);
  
        return $data;
    }

    public function getAssets() {
        return $this->DataAssetInstance->Asset->getList();
    }

    public function getBusinessUnits() {
        return $this->BusinessUnit->getList();
    }

    public function getThirdParties() {
        return $this->ThirdParty->getList();
    }

    public function getRisks() {
        return $this->Risk->getList();
    }

    public function getThirdPartyRisks() {
        return $this->ThirdPartyRisk->getList();
    }

    public function getBusinessContinuities() {
        return $this->BusinessContinuity->getList();
    }

    public function getSecurityServices() {
        return $this->SecurityService->getList();
    }

    public function getNotDesignSecurityServices() {
        return ClassRegistry::init('SecurityService')->find('list', [
            'conditions' => [
                'SecurityService.security_service_type_id !=' => SECURITY_SERVICE_DESIGN
            ],
            'order' => ['SecurityService.name' => 'ASC'],
            'recursive' => -1
        ]);
    }

    public function getSecurityPolicies() {
        return $this->SecurityPolicy->getListWithType();
    }

    public function getProjects() {
        return $this->Project->getList();
    }

    public function getEuropeCountries() {
        return Country::europeCountries();
    }

    public function getCountries() {
        return Country::countries();
    }

    public static function dataTypes($value = null) {
        return DataAssetGdprDataType::dataTypes($value);
    }

    public static function collectionMethods($value = null) {
        return DataAssetGdprCollectionMethod::collectionMethods($value);
    }

    public static function lawfulBases($value = null) {
        return DataAssetGdprLawfulBase::lawfulBases($value);
    }

    public static function thirdPartyCountries($value = null) {
        return DataAssetGdprThirdPartyCountry::thirdPartyCountries($value);
    }

    public static function archivingDrivers($value = null) {
        return DataAssetGdprArchivingDriver::archivingDrivers($value);
    }

    public function getUsers() {
        $User = ClassRegistry::init('User');

        $User->virtualFields['full_name'] = 'CONCAT(User.name, " ", User.surname)';
        $users = $User->find('list', [
            'conditions' => [],
            'fields' => ['User.id', 'User.full_name'],
        ]);

        return $users;
    }

    public function isGdprEnabled($dataAssetInstanceId) {
        $result = $this->DataAssetInstance->DataAssetSetting->field('gdpr_enabled', [
            'DataAssetSetting.data_asset_instance_id' => $dataAssetInstanceId
        ]);

        return !empty($result) ? true : false;
    }

    public function hasSectionIndex()
    {
        return true;
    }
}
