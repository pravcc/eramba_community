<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('AppIndexCrudAction', 'Controller/Crud/Action');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('UserFields', 'UserFields.Lib');

class Asset extends AppModel
{
	public $displayField = 'name';
	
	public $findMethods = array('self' =>  true);

	private $reviewAfterSave = false;

	public $actsAs = array(
		'Search.Searchable',
		'AuditLog.Auditable' => array(
			'ignore' => array(
				'security_incident_open_count',
				'created',
				'modified',
				'Risk',
				'ThirdPartyRisk',
				'SecurityIncident'
			)
		),
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report'
			]
		],
		'Visualisation.Visualisation',
		'ReviewsPlanner.Reviews' => [
			'dateColumn' => 'review',
			'userFields' => [
				'AssetOwner',
				'AssetGuardian',
				'AssetUser'
			]
		],
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => [
				'AssetOwner',
				'AssetGuardian' => [
					'mandatory' => false
				],
				'AssetUser' => [
					'mandatory' => false
				]
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedQuery.AdvancedFinder',
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
		'asset_media_type_id' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		'review' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			),
			'date' => array(
				'rule' => 'date',
				'required' => true,
				'message' => 'This date has incorrect format'
			)
		),
		'BusinessUnit' => array(
			'rule' => array( 'multiple', array( 'min' => 1 ))
		)
	);

	public $belongsTo = array(
		'AssetMediaType',
		'AssetLabel',
		/*'AssetMainContainer' => array(
			'className' => 'Asset',
			'foreignKey' => 'asset_id'
		)*/
	);

	public $hasOne = array(
		'DataAssetInstance'
	);

	public $hasMany = array(
		'AssetReview' => array(
			'className' => 'AssetReview',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'AssetReview.model' => 'Asset'
			)
		),
		'Review' => array(
			'className' => 'Review',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Review.model' => 'Asset'
			)
		),
		// 'WorkflowAcknowledgement' => array(
		// 	'className' => 'WorkflowAcknowledgement',
		// 	'foreignKey' => 'foreign_key',
		// 	'conditions' => array(
		// 		'WorkflowAcknowledgement.model' => 'Asset'
		// 	)
		// )
	);

	public $hasAndBelongsToMany = array(
		'BusinessUnit',
		'Legal',
		'AssetClassification',
		'Risk',
		'ThirdPartyRisk',
		'SecurityIncident',
		'RelatedAssets' => array(
			'className' => 'Asset',
			'with' => 'AssetsRelated',
			'joinTable' => 'assets_related',
			'foreignKey' => 'asset_id',
			'associationForeignKey' => 'asset_related_id'
		),
		'ComplianceManagement',
		// 'AccountReview' => [
		// 	'className' => 'AccountReviews.AccountReview',
		// ],
	);

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
		
		$this->label = __('Asset Identification');
		$this->_group = parent::SECTION_GROUP_ASSET_MGT;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
			'asset-owner' => array(
				'label' => __('Asset Owner')
			),
			'asset-classification' => array(
				'label' => __('Asset Classification')
			)
		);

		$this->fieldData = array(
			'BusinessUnit' => array(
				'label' => __('Related Business Units'),
				// 'options' => array($this, 'getBusinessUnits'),
				'editable' => true,
				'description' => __( 'One or more Business Units (Organisation / Business Units) that are associated with this asset' ),
				'renderHelper' => ['Assets', 'businessUnitsField'],
				'quickAdd' => true,
				'inlineEdit' => true,
			),
			'name' => array(
				'label' => __('Name'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Name of the asset')
			),
			'description' => array(
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Give a brief description for this asset')
			),
			'asset_label_id' => array(
				'label' => __('Label'),
				// 'options' => array($this, 'getLabels'),
				'editable' => true,
				'description' => __('OPTIONAL: Labels refer to the type of asset being created, typical examples are Confidential, Private, Etc. Labels are defined at Asset Management/Settings/Labels'),
				'empty' => __('Choose one'),
				'quickAdd' => true,
				'inlineEdit' => true,
			),
			'asset_media_type_id' => array(
				'label' => __('Type'),
				// 'options' => array($this, 'getMediaTypes'),
				'editable' => true,
				'description' => __('Based on the type of asset eramba will suggest you possible threats and vulnerabilities (at the time you perform Risk Management). If you are also interested in Data Asset Flows (Asset Management / Data Flows) you need to select "Data Asset"'),
				'quickAdd' => true,
				'inlineEdit' => true,
			),
			'RelatedAssets' => array(
				'label' => __('Assets'),
				// 'options' => array($this, 'getAssets'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Select one or more related assets'),
				'validates' => [
					'mandatory' => false
				]
			),
			'Legal' => array(
				'label' => __('Liabilities'),
				// 'options' => array($this, 'getLegals'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Select one or more liabilities (Organisation / Liabilities) that are asociated with this asset'),
				'renderHelper' => ['Assets', 'legalField'],
				'validates' => [
					'mandatory' => false
				],
				'quickAdd' => true,
			),
			'review' => array(
				'label' => __('New Review Date'),
				'editable' => true,
				'inlineEdit' => false,
				'description' =>  __('Select a date when this asset should be reviewed. After creating this assets two review records will be created - one with todays date and another with the date you set on this field'),
				'renderHelper' => ['Assets', 'reviewField'],
			),
			'AssetOwner' => $UserFields->getFieldDataEntityData($this, 'AssetOwner', [
				'label' => __('Owner'), 
				'description' => __('Select one or more user accounts or groups that are asociated as owners of this asset'),
				'group' => 'asset-owner',
				'dependency' => true,
				'inlineEdit' => true,
				'quickAdd' => true
			]),
			'AssetGuardian' => $UserFields->getFieldDataEntityData($this, 'AssetGuardian', [
				'label' => __('Guardian'), 
				'description' => __('Select one or more user accounts or groups that are asociated as guardians of this asset'),
				'group' => 'asset-owner',
				'dependency' => true,
				'inlineEdit' => true,
				'quickAdd' => true
			]),
			'AssetUser' => $UserFields->getFieldDataEntityData($this, 'AssetUser', [
				'label' => __('User'), 
				'description' => __('Select one or more user accounts or groups that are asociated as users of this asset'),
				'group' => 'asset-owner',
				'dependency' => true,
				'inlineEdit' => true,
				'quickAdd' => true
			]),
			'AssetClassification' => array(
				'type' => 'select',
				'label' => __('Classification'),
				'group' => 'asset-classification',
				'options' => array($this, 'getClassifications'),
				'description' => __('If you defined Asset Classifications (Asset Management / Asset Identification / Settings / Classifications) you can now classify this asset. This will only be useful if you plan to use "Magerit" as a Risk Management calculation methodology (Risk Management/ Asset Risk Management / Settings / Risk Calculation)'),
				'editable' => true,
				'inlineEdit' => false,
				'empty' => __('Choose Classification'),
                'renderHelper' => ['Assets', 'assetClassificationField'],
                // 'Extensions' => [
                //     'AssetClassification'
                // ],
                'validates' => [
                	'mandatory' => false
                ],
                'quickAdd' => true,
			),
			'expired_reviews' => array(
				'label' => __('Expired Reviews'),
				'toggle' => true,
				'hidden' => true
			),
			'Risk' => [
				'label' => __('Asset Risk'),
				'editable' => false,
				'hidden' => true,
			],
			'ThirdPartyRisk' => [
				'label' => __('Third Party Risk'),
				'editable' => false,
				'hidden' => true,
			],
			'SecurityIncident' => [
				'label' => __('Security Incident'),
				'editable' => false,
				'hidden' => true,
			],
			'AccountReview' => [
				'label' => __('Account Review'),
				'editable' => false,
				'hidden' => true,
				'usable' => false
			],
		);

		$this->notificationSystem = array(
			'macros' => array(
				'ASSET_ID' => array(
					'field' => 'Asset.id',
					'name' => __('Asset ID')
				),
				'ASSET_NAME' => array(
					'field' => 'Asset.name',
					'name' => __('Asset Name')
				),
				'ASSET_DESCRIPTION' => array(
					'field' => 'Asset.description',
					'name' => __('Asset Description')
				),
				'ASSET_REVIEW_DATE' => array(
					'field' => 'Asset.review',
					'name' => __('Asset Review')
				)
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Assets'),
			'pdf_file_name' => __('assets'),
			'csv_file_name' => __('assets'),
			'max_selection_size' => 30,
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
            'view_item' => AppIndexCrudAction::VIEW_ITEM_QUERY,
			'additional_actions' => array(
				'AssetReview' => array(
					'label' => __('Reviews'),
					'url' => array(
						'controller' => 'reviews',
						'action' => 'filterIndex',
						'AssetReview',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
			),
			'use_new_filters' => true,
			'add' => true,
		);

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		if (AppModule::loaded('AccountReviews') && $this->getAssociated('AccountReview') === null) {
			$this->bindModel([
	            'hasAndBelongsToMany' => [
	                'AccountReview' => [
	                    'className' => 'AccountReviews.AccountReview',
	                ]
	            ]
	        ], false);
		}

		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->multipleSelectField('BusinessUnit', [ClassRegistry::init('BusinessUnit'), 'getList'], [
					'showDefault' => true
				])
				->textField('name', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->multipleSelectField('asset_label_id', [ClassRegistry::init('AssetLabel'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('asset_media_type_id', [ClassRegistry::init('AssetMediaType'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('Legal', [ClassRegistry::init('Legal'), 'getList'], [
					'showDefault' => true
				])
				->dateField('review', [
					'label' => __('Review'),
					'showDefault' => true
				])
				->multipleSelectField('RelatedAssets', [$this, 'getList'], [
					'label' => __('Related Assets') 
				])
				->objectStatusField('ObjectStatus_expired_reviews', 'expired_reviews')
			->group('owner', [
				'name' => __('Owner')
			])
				->userField('AssetOwner', 'AssetOwner', [
					'showDefault' => true
				])
				->userField('AssetGuardian', 'AssetGuardian', [
					'showDefault' => true
				])
				->userField('AssetUser', 'AssetUser', [
					'showDefault' => true
				])
			->group('AssetClassification', [
				'name' => __('Asset Classification')
			])
				->multipleSelectField('AssetClassification', [$this, 'getClassifications']);

			if (AppModule::loaded('AccountReviews')) {
				$advancedFilterConfig->group('AccountReview', [
					'name' => __('Account Review')
				])
					->multipleSelectField('AccountReview', [ClassRegistry::init('AccountReviews.AccountReview'), 'getList'])
					->multipleSelectField('AccountReview-status', [ClassRegistry::init('AccountReviews.AccountReview'), 'statuses'], [
						'label' => __('Account Review Status')
					]);
			}

		$this->SecurityIncident->relatedFilters($advancedFilterConfig);
		$this->Risk->relatedFilters($advancedFilterConfig);
		$this->ThirdPartyRisk->relatedFilters($advancedFilterConfig);
		$this->ComplianceManagement->relatedFilters($advancedFilterConfig);

		$advancedFilterConfig
			->group('DataAsset', [
				'name' => __('Data Flow Analysis')
			])
				->multipleSelectField('DataAssetInstance-DataAsset', [ClassRegistry::init('DataAsset'), 'getList'], [
					'label' => __('Data Asset Flow'),
				])
				->multipleSelectField('DataAsset-data_asset_status_id', [ClassRegistry::init('DataAsset'), 'statuses'], [
					'label' => __('Data Asset Flow Type'),
					'findField' => 'DataAssetInstance.DataAsset.data_asset_status_id',
					'fieldData' => 'DataAssetInstance.DataAsset.data_asset_status_id',
				])
				->selectField('DataAssetSetting-gdpr_enabled', [ClassRegistry::init('DataAssetSetting'), 'getStatusFilterOption'], [
					'label' => __('Data Asset GDPR Enabled'),
					'findField' => 'DataAssetInstance.DataAssetSetting.gdpr_enabled',
					'fieldData' => 'DataAssetInstance.DataAssetSetting.gdpr_enabled',
				])
				->selectField('DataAssetInstance-incomplete_gdpr_analysis', [ClassRegistry::init('DataAssetSetting'), 'getStatusFilterOption'], [
					'label' => __('Data Asset Incomplete GDPR Analysis'),
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
			->group('Asset', [
				'name' => __('Asset Identification')
			])
				->multipleSelectField('Asset', [$this, 'getList'], [
					'label' => __('Asset')
				]);

		return $advancedFilterConfig;
	}

	public function getImportToolConfig()
	{
		return [
			'Asset.BusinessUnit' => [
				'name' => __('Related Business Units'),
				'model' => 'BusinessUnit',
				'headerTooltip' => __('This field is mandatory, you need to input one or more Business Units Names. You can get the name of a business unit from Organisation / Business Units - eramba will check if those names exist and if they dont, block the import - If you want to include more than one, remember to split them with "|". For example Finance|Sales would include two business units.'),
				'objectAutoFind' => true
			],
			'Asset.name' => [
				'name' => __('Name'),
				'headerTooltip' => __('This field is mandatory, simply put the asset name. For example: Corporate Laptops.')
			],
			'Asset.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('This field is not mandatory, you can leave it blank if you want.')
			],
			'Asset.asset_label_id' => [
				'name' => __('Label'),
				'model' => 'AssetLabel',
				'headerTooltip' => __('This field is not mandatory, you can leave it blank. Alternatively you can include the Asset Label Name which you can get from Asset Management / Asset Identification / Settings / Labels.'),
				'objectAutoFind' => true
			],
			'Asset.asset_media_type_id' => [
				'name' => __('Type'),
				'model' => 'AssetMediaType',
				'headerTooltip' => __('This field is mandatory, set the Asset Type Name which you can get from Asset Management / Asset Identification / Settings / Asset Types.'),
				'objectAutoFind' => true
			],
			'Asset.Legal' => [
				'name' => __('Liabilities'),
				'model' => 'Legal',
				'headerTooltip' => __('This is an optional field, you can leave it blank if you want to, alternatively you need to insert the Name of one or more liabilities. You can get the Name for each existing liability at Organisation  / Legal Constrains. Eramba will check each liability exists! If you want to include more than one, remember to split them with a "|" character. For example: Legal|Legal2.'),
				'objectAutoFind' => true
			],
			'Asset.review' => [
				'name' => __('Review'),
				'headerTooltip' => __('This is a mandatory field, it must have the format YYYY-MM-DD - bare In ind the "-" delimiter.')
			],
			'Asset.AssetOwner' => UserFields::getImportArgsFieldData('AssetOwner', [
				'name' => $this->getFieldCollection()->get('AssetOwner')->getLabel()
			]),
			'Asset.AssetGuardian' => UserFields::getImportArgsFieldData('AssetGuardian', [
				'name' => $this->getFieldCollection()->get('AssetGuardian')->getLabel()
			], true),
			'Asset.AssetUser' => UserFields::getImportArgsFieldData('AssetUser', [
				'name' => $this->getFieldCollection()->get('AssetUser')->getLabel()
			], true),
		];
	}

	public function getSectionInfoConfig()
    {
        return [
            'description' => __('Assets defined in this section are required for Risk modules, Data Flow Analysis and optional for a multitude of othe modules in the system'),
			'map' => [
				'SecurityIncident' => [
					'SecurityIncidentStage',
				],
				'DataAssetInstance' => [
					'DataAsset',
				],
				'ThirdPartyRisk',
				'Risk',
				'AccountReview',
				'ComplianceManagement',
			]
        ];
    }

	public function getObjectStatusConfig() {
        return [
            'expired_reviews' => [
            	'title' => __('Review Expired'),
                'callback' => [$this, '_statusExpiredReviews'],
                'trigger' => [
                	[
                        'model' => $this->DataAssetInstance,
                        'trigger' => 'ObjectStatus.trigger.asset_missing_review'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'current_review_trigger' => [
                'trigger' => [
                    [
                        'model' => $this->AssetReview,
                        'trigger' => 'ObjectStatus.trigger.current_review'
                    ],
                ],
                'hidden' => true
            ],
            'ongoing_incident' => [
            	'title' => __('Incident Ongoing'),
                'inherited' => [
                	'SecurityIncident' => 'ongoing_incident'
            	],
            	'storageSelf' => false
            ],
        ];
    }

    public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();

		return $config;
	}

	public function getReportsConfig()
	{
		return [
			'finder' => [
				'options' => [
					'contain' => [
						'AssetMediaType',
						'AssetLabel',
						'DataAssetInstance' => [
							'Asset',
							'DataAssetSetting',
							'DataAsset'
						],
						'AssetReview',
						'Review' => [
							'User'
						],
						'CustomFieldValue',
						'RelatedAssets',
						'BusinessUnit' => [
							'Process',
							'CustomFieldValue',
							'Asset',
							'BusinessContinuity',
							'Legal',
							'BusinessUnitOwner',
							'BusinessUnitOwnerGroup'
						],
						'Legal' => [
							'CustomFieldValue',
							'Asset',
							'BusinessUnit',
							'ThirdParty',
							'LegalAdvisor',
							'LegalAdvisorGroup'
						],
						'AssetClassification' => [
							'AssetClassificationType'
						],
						'Risk',
						'ThirdPartyRisk',
						'SecurityIncident',
						'AssetOwner',
						'AssetOwnerGroup',
						'AssetGuardian',
						'AssetGuardianGroup',
						'AssetUser',
						'AssetUserGroup',
						'ComplianceManagement' => [
							'CompliancePackageItem' => [
								'CompliancePackage'
							]
						]
					]
				]
			],
			'table' => [
				'model' => [
					'DataAssetInstance', 'Review', 'BusinessUnit', 'Legal'
				]
			],
			'chart' => [
				1 => [
					'title' => __('Asset and related Objects'),
					'description' => __('This tree shows the asset and its asociated risks, compliance packages, incidents and account reviews.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'relatedObjectsChart'
				],
				2 => [
					'title' => __('Assets by Classification'),
					'description' => __('This chart shows the proportion of assets based on their classification.'),
					'type' => ReportBlockChartSetting::TYPE_SUNBURST,
					'templateType' => ReportTemplate::TYPE_SECTION,
					'className' => 'AssetsByClassificationChart',
				],
				3 => [
					'title' => __('Assets by Label'),
					'description' => __('This chart shows the proportion of assets based on their labels.'),
					'type' => ReportBlockChartSetting::TYPE_PIE,
					'templateType' => ReportTemplate::TYPE_SECTION,
					'className' => 'CollectionByProperty',
					'params' => [
						'property' => 'AssetLabel'
					]
				],
			]
		];
	}

	public function notEveryone($check) {
        $value = array_values($check);
        $value = $value[0];
        
       	if (in_array(BU_EVERYONE, $value)) {
       		return false;
       	}

        return true;
    }

	public function beforeValidate($options = array()) {
		if (isset($this->data['Asset']['BusinessUnit'])) {
			$this->invalidateRelatedNotExist('BusinessUnit', 'BusinessUnit', $this->data['Asset']['BusinessUnit']);
		}

		if (isset($this->data['Asset']['Legal'])) {
			$this->invalidateRelatedNotExist('Legal', 'Legal', $this->data['Asset']['Legal']);
		}

		if (isset($this->data['Asset']['asset_media_type_id'])) {
			$this->invalidateRelatedNotExist('AssetMediaType', 'asset_media_type_id', $this->data['Asset']['asset_media_type_id']);
		}

		if (isset($this->data['Asset']['asset_label_id'])) {
			$this->invalidateRelatedNotExist('AssetLabel', 'asset_label_id', $this->data['Asset']['asset_label_id']);
		}
	}

	public function printMediaTypes(&$item, $key) {
		$item = sprintf("\n%s: %s", $key, $item);
	}

	public function beforeSave($options = array()) {
		// $ret = $this->createReview();

		// $this->transformDataToHabtm(array('Legal', 'BusinessUnit', 'RelatedAssets', 'AssetClassification'));

		return true;
	}

	public function afterSave($created, $options = array())
	{
		if (isset($options['customCallbacks']['Asset']['after']) &&
			$options['customCallbacks']['Asset']['after'] == false) {
			return true;
		}

		if ($this->reviewAfterSave && !empty($this->data['Asset']['review'])) {
			$this->saveReviewRecord($this->data['Asset']['review']);
		}

		if ($created === false) {
			$this->setRisksData();
			$this->updateRiskScores();
		}

		$this->createDataAssetInstance($this->id);
	}

	/**
	 * Create new DataAssetInstance for input $assetId.
	 * 
	 * @param  int $assetId
	 * @return boolean Success.
	 */
	public function createDataAssetInstance($assetId) {
		$ret = true;

		$asset = $this->find('count', [
			'conditions' => [
				'Asset.id' => $assetId,
				'Asset.asset_media_type_id' => ASSET_MEDIA_TYPE_DATA
			],
			'recursive' => -1
		]);

		if (empty($asset)) {
			$this->DataAssetInstance->deleteAll(['asset_id' => $assetId]);
			return $ret;
		}

		$instance = $this->DataAssetInstance->find('count', [
			'conditions' => [
				'DataAssetInstance.asset_id' => $assetId
			],
			'recursive' => -1
		]);

		if (empty($instance)) {
			$this->DataAssetInstance->create();
			$ret &= $this->DataAssetInstance->save([
				'asset_id' => $assetId
			]);
		}

		return $ret;
	}

	public function saveJoins($data = null) {
		$this->data = $data;

		$ret = true;

		$ret &= $this->joinHabtm('BusinessUnit', 'business_unit_id');
		$ret &= $this->joinHabtm('AssetClassification', 'asset_classification_id');
		$ret &= $this->joinHabtm('Legal', 'legal_id');
		$ret &= $this->joinHabtm('RelatedAssets', 'related_id');

		$this->data = false;
		
		return $ret;
	}

	public function deleteJoins($id) {
		$ret = $this->AssetsBusinessUnit->deleteAll( array(
			'AssetsBusinessUnit.asset_id' => $id
		) );
		$ret &= $this->AssetClassificationsAsset->deleteAll( array(
			'AssetClassificationsAsset.asset_id' => $id
		) );
		$ret &= $this->AssetsLegal->deleteAll( array(
			'AssetsLegal.asset_id' => $id
		) );
		$ret &= $this->AssetsRelated->deleteAll( array(
			'AssetsRelated.asset_id' => $id
		) );

		return $ret;
	}

	// private function createReview() {
	// 	if (!isset($this->data['Asset']['review'])) {
	// 		return true;
	// 	}

	// 	if (!empty($this->id)) {
	// 		$data = $this->find('first', array(
	// 			'conditions' => array(
	// 				'Asset.id' => $this->id
	// 			),
	// 			'fields' => array('Asset.review'),
	// 			'recursive' => -1
	// 		));

	// 		if ($data['Asset']['review'] == $this->data['Asset']['review']) {
	// 			return true;
	// 		}

	// 		return $this->saveReviewRecord($this->data['Asset']['review']);
	// 	}

	// 	$this->reviewAfterSave = true;
	// 	return true;
	// }

	/**
	 * Save an actual review item.
	 *
	 * @param  string $review Date.
	 */
	// private function saveReviewRecord($review) {
	// 	$user = $this->currentUser();
	// 	$saveData = array(
	// 		'model' => 'Asset',
	// 		'foreign_key' => $this->id,
	// 		'planned_date' => $review,
	// 		'user_id' => $user['id'],
	// 	);

	// 	$this->Review->set($saveData);
	// 	return $this->Review->save(null, false);
	// }

	public function beforeDelete($cascade = true) {
		$ret = $this->deleteUselessRisk();
		$this->setRisksData();

		return $ret;
	}

	public function afterDelete() {
		$this->updateRiskScores();
	}

	private function deleteUselessRisk() {
		$data = $this->AssetsRisk->find('all', array(
			'conditions' => array(
				'AssetsRisk.asset_id' => $this->id
			)
		));

		$ret = true;
		foreach ($data as $risk) {
			$count = $this->AssetsRisk->find('count', array(
				'conditions' => array(
					'AssetsRisk.risk_id' => $risk['AssetsRisk']['risk_id']
				)
			));

			if ($count == 1) {
				$ret &= $this->Risk->delete($risk['AssetsRisk']['risk_id']);
			}
		}

		return $ret;
	}

	public function setRisksData() {
		$data = $this->find('all', array(
			'conditions' => array(
				'Asset.id' => $this->id
			),
			'contain' => array(
				'Risk' => array(
					'fields' => array('id')
				),
				'ThirdPartyRisk' => array(
					'fields' => array('id')
				)
			)
		));

		if (!isset($this->RiskIds)) {
			$this->RiskIds = $this->TpRiskIds = array();
		}

		foreach ($data as $asset) {
			foreach ($asset['Risk'] as $risk) {
				$this->RiskIds[] = $risk['id'];
			}

			foreach ($asset['ThirdPartyRisk'] as $risk) {
				$this->TpRiskIds[] = $risk['id'];
			}
		}
	}

	public function getAssets() {
		$data = $this->find('list', array(
			'conditions' => array(
				'workflow_status' => WORKFLOW_APPROVED
			),
			'order' => array('name' => 'ASC'),
			'recursive' => -1
		));

		return $data;
	}

	public function getBusinessUnits() {
		$data = $this->BusinessUnit->find('list', array(
			'order' => array('BusinessUnit.name' => 'ASC'),
			'fields' => array('BusinessUnit.id', 'BusinessUnit.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getDataAssets() {
		$data = $this->DataAsset->find('list', array(
			'order' => array('DataAsset.description' => 'ASC'),
			'fields' => array('DataAsset.id', 'DataAsset.description'),
			'recursive' => -1
		));

		return $data;
	}

	public function getLabels() {
		$data = $this->AssetLabel->find('list', array(
			'order' => array('AssetLabel.name' => 'ASC'),
			'fields' => array('AssetLabel.id', 'AssetLabel.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getMediaTypes() {
		$data = $this->AssetMediaType->find('list', array(
			'order' => array('AssetMediaType.name' => 'ASC'),
			'fields' => array('AssetMediaType.id', 'AssetMediaType.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getLegals() {
		$data = $this->Legal->find('list', array(
			'order' => array('Legal.name' => 'ASC'),
			'fields' => array('Legal.id', 'Legal.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getOwners()	{
		$data = $this->AssetOwner->find('list', array(
			'order' => array('AssetOwner.name' => 'ASC'),
			'fields' => array('AssetOwner.id', 'AssetOwner.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getReviews() {
		$data = $this->Review->find('list', array(
			'conditions' => array(
				'Review.model' => 'Asset'
			),
			'order' => array('Review.planned_date' => 'ASC'),
			'fields' => array('Review.id', 'Review.planned_date'),
			'recursive' => -1
		));
		return $data;
	}

	public function getClassifications() {
		$dataRaw = $this->AssetClassification->find('all', array(
			'order' => array('AssetClassification.name' => 'ASC'),
			'fields' => array('AssetClassification.id', 'AssetClassification.name', 'AssetClassificationType.name'),
			'contain' => array(
				'AssetClassificationType'
			),
		));

		$data = array();
		foreach ($dataRaw as $item) {
			$data[$item['AssetClassification']['id']] = '[' . $item['AssetClassificationType']['name'] . '] - ' . $item['AssetClassification']['name'];
		}

		return $data;
	}

	public function findByReviews($data = array()) {
		$this->Review->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->Review->Behaviors->attach('Search.Searchable');

		$query = $this->Review->getQuery('all', array(
			'conditions' => array(
				'Review.id' => $data['review_id']
			),
			'fields' => array(
				'Review.foreign_key'
			),
			'recursive' => -1
		));

		return $query;
	}

	public function findByBusinessUnits() {
		$this->AssetsBusinessUnit->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->AssetsBusinessUnit->Behaviors->attach('Search.Searchable');

		$query = $this->AssetsBusinessUnit->getQuery('all', array(
			'conditions' => array(
				'AssetsBusinessUnit.business_unit_id' => $data['business_units']
			),
			'fields' => array(
				'AssetsBusinessUnit.asset_id'
			),
			'recursive' => -1
		));

		return $query;
	}

	public function updateRiskScores() {
		$this->Risk->calculateAndSaveRiskScoreById($this->RiskIds);
		$this->ThirdPartyRisk->calculateAndSaveRiskScoreById($this->TpRiskIds);
	}

	public function getThreatsVulnerabilities($assetIds) {
		$typeIds = $this->find('list', array(
			'conditions' => array(
				'Asset.id' => $assetIds
			),
			'fields' => array('asset_media_type_id'),
			'recursive' => -1
		));

		return $this->AssetMediaType->getThreatsVulnerabilities($typeIds);
	}

	public function getAssetClassificationsData() {
		$this->AssetClassification->bindModel(array(
			'hasMany' => array(
				'AssetClassificationsAsset'
			)
		));

		$data = $this->AssetClassification->find('all', array(
			'fields' => array('AssetClassification.name', 'AssetClassification.value', 'AssetClassification.criteria'),
			'contain' => array(
				'AssetClassificationType' => array(
					'fields' => array('id', 'name')
				),
				'AssetClassificationsAsset' => array(
					'fields' => array('asset_id')
				)
			)
		));

		$formattedData = $joinAssets = array();
		foreach ($data as $classification) {
			$formattedData[$classification['AssetClassification']['id']] = $classification;

			foreach ($classification['AssetClassificationsAsset'] as $join) {
				if (!isset($joinAssets[$join['asset_id']])) {
					$joinAssets[$join['asset_id']] = array();
				}

				$joinAssets[$join['asset_id']][] = $join['asset_classification_id'];
			}
		}

		return array(
			'formattedData' => $formattedData,
			'joinAssets' => $joinAssets
		);
	}

	/**
	 * Add missing DataAssetInstance records for all assets.
	 *
	 * @return  boolean Success.
	 */
	public function addMissingInstances() {
		$Instance = ClassRegistry::init('DataAssetInstance');

		$assets = $this->find('all', ['recursive' => -1]);

		$ret = true;

		foreach ($assets as $asset) {
			$ret &= $this->createDataAssetInstance($asset['Asset']['id']);
		}

		return $ret;
	}

	public function hasSectionIndex()
	{
		return true;
	}

}
