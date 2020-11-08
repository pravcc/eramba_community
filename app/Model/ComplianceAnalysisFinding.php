<?php
App::uses('AppModel', 'Model');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('UserFields', 'UserFields.Lib');

class ComplianceAnalysisFinding extends AppModel
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
		'notificationSystem' => array('index')
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'description'
			)
		),
		'AuditLog.Auditable' => array(
			'ignore' => array(
				'created',
				'modified',
			)
		),
		'Utils.SoftDelete',
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
				'Owner',
				'Collaborator' => [
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
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'due_date' => array(
			'rule' => 'date',
			'required' => true
		),
		'CompliancePackageRegulator' => array(
			'minCount' => array(
				'rule' => array('multiple', array('min' => 1)),
				'message' => 'You have to select at least one Compliance Package'
			)
		),
		'CompliancePackageItem' => array(
			'minCount' => array(
				'rule' => array('multiple', array('min' => 1)),
				'message' => 'You have to select at least one Compliance Package Item'
			),
			'validatePackageItems' => array(
				'rule' => 'validatePackageItems',
				'message' => 'You have to select Complince Package Items of selected Complince Package'
			)
		),
		// 'asset_owner_id' => array(
		// 	'rule' => 'notBlank',
		// 	'required' => true,
		// 	'allowEmpty' => false
		// ),
		// 'business_unit_id' => array(
		// 	'rule' => array( 'multiple', array( 'min' => 1 ) )
		// ),
		// 'asset_media_type_id' => array(
		// 	'rule' => 'notBlank',
		// 	'required' => true
		// ),
		// 'review' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notBlank',
		// 		'required' => true,
		// 		'message' => 'This field is required'
		// 	),
		// 	'date' => array(
		// 		'rule' => 'date',
		// 		'required' => true,
		// 		'message' => 'This date has incorrect format'
		// 	)
		// )
	);

	// public $belongsTo = array(
	// );

	public $hasMany = array(
		'Tag' => [
			'className' => 'Tag',
			'foreignKey' => 'foreign_key',
			'conditions' => [
				'Tag.model' => 'ComplianceAnalysisFinding'
			]
		]
	);

	public $hasAndBelongsToMany = array(
		'ComplianceManagement' => array(
			'with' => 'ComplianceAnalysisFindingsComplianceManagement'
		),
		// 'CompliancePackage',
		'CompliancePackageRegulator',
		'CompliancePackageItem'
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Compliance Analysis Finding');
        $this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General'),
			),
			'compliance-items' => array(
				'label' => __('Affected Compliance Items'),
			),
		);

		$this->fieldData = [
			'title' => array(
				'label' => __('Name'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Provide a title for this finding, such as "FIND01 - Missing Firewall Policies"'),
			),
			'description' => array(
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the finding.'),
			),
			'due_date' => array(
				'label' => __('Due Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Input the date by which this compliance findings must be resolved, you can assign notifications to remind your team about this deadline.'),
			),
			'status' => array(
				'label' => __('Status'),
				'options' => array($this, 'statuses'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Set the status of this compliance finding as "open" or "closed" (once is resolved).'),
			),
			'expired' => array(
				'label' => __('Expired'),
				'type' => 'toggle',
				'editable' => false,
				'hidden' => true,
			),
			'Tag' => array(
                'label' => __('Tags'),
				'editable' => true,
				'type' => 'tags',
				'description' => __('You can use tags to profile your findings, examples are "In Remediation", "Networks", Etc.'),
				'empty' => __('Add a tag')
            ),
            'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'), 
				'description' => __('Use this field to select one or more individuals that are accountable to get this issue sorted out. This is typically someone at the GRC department.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'Collaborator' => $UserFields->getFieldDataEntityData($this, 'Collaborator', [
				'label' => __('Collaborator'), 
				'description' => __('Use this field to select one or more individuals that are responsible to get this issue sorted out. This is typically someone at the department where the finding was found, this could be a Technical department, Etc.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'ComplianceManagement' => array(
				'label' => __('Compliance Management'),
				'editable' => false,
				'hidden' => true
			),
			'CompliancePackageRegulator' => [
				'label' => __('Compliance Package'),
				'editable' => true,
				'quickAdd' => true,
				'group' => 'compliance-items',
				'renderHelper' => ['ComplianceAnalysisFindings', 'compliancePackageRegulatorField'],
				'inlineEdit' => false,
			],
			'CompliancePackageItem' => [
				'label' => __('Compliance Package Item'),
				'editable' => true,
				'group' => 'compliance-items',
				'renderHelper' => ['ComplianceAnalysisFindings', 'compliancePackageItemField'],
				'inlineEdit' => false,
			],
		];

		$this->notificationSystem = array(
			'macros' => array(
				'FINDING_ID' => array(
					'field' => 'ComplianceAnalysisFinding.id',
					'name' => __('Finding ID')
				),
				'FINDING_NAME' => array(
					'field' => 'ComplianceAnalysisFinding.title',
					'name' => __('Finding Title')
				),
				'FINDING_DESCRIPTION' => array(
					'field' => 'ComplianceAnalysisFinding.description',
					'name' => __('Finding Description')
				),
				'FINDING_DUE_DATE' => array(
					'field' => 'ComplianceAnalysisFinding.due_date',
					'name' => __('Finding Due Date')
				)
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => $this->label,
			'pdf_file_name' => __('compliance_analysis_findings'),
			'csv_file_name' => __('compliance_analysis_findings'),
			'max_selection_size' => 10,
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
			'use_new_filters' => true,
			'add' => true,
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
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->dateField('due_date', [
					'showDefault' => true
				])
				->multipleSelectField('Tag-title', [$this, 'getTags'], [
					'showDefault' => true
				])
				->userField('Owner', 'Owner', [
					'showDefault' => true
				])
				->userField('Collaborator', 'Collaborator', [
					'showDefault' => true
				])
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_expired', 'expired');

		$this->ComplianceManagement->relatedFilters($advancedFilterConfig);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getImportToolConfig()
	{
		return [
			'ComplianceAnalysisFinding.title' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory')
			],
			'ComplianceAnalysisFinding.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('Optional, you can leave this field blank')
			],
			'ComplianceAnalysisFinding.due_date' => [
				'name' => __('Due Date'),
				'headerTooltip' => __('This field is mandatory')
			],
			'ComplianceAnalysisFinding.Tag' => [
				'name' => __('Tags'),
				'model' => 'Tag',
				'callback' => [
					'beforeImport' => [$this, 'convertTagsImport']
				],
				'headerTooltip' => __('Optional and accepts tags separated by "|". For example "Critical|SOX|PCI"')
			],
			'ComplianceAnalysisFinding.Owner' => UserFields::getImportArgsFieldData('Owner', [
				'name' => __('Owner')
			]),
			'ComplianceAnalysisFinding.Collaborator' => UserFields::getImportArgsFieldData('Collaborator', [
				'name' => __('Collaborator')
			]),
			'ComplianceAnalysisFinding.status' => [
				'name' => __('Status'),
				'headerTooltip' => __(
					'Mandatory, set value: %s',
					ImportToolModule::formatList($this->statuses(), false)
				)
			],
			'ComplianceAnalysisFinding.CompliancePackageRegulator' => [
				'name' => __('Compliance Packages'),
				'model' => 'CompliancePackageRegulator',
				'headerTooltip' => __('Mandatory, accepts multiple names separated by "|". You need to enter the name of a Compliance Package, you can find them at Compliance Mgt / Compliance Packages'),
				'objectAutoFind' => true
			],
			'ComplianceAnalysisFinding.CompliancePackageItem' => [
				'name' => __('Compliance Package Items'),
				'model' => 'CompliancePackageItem',
				'headerTooltip' => __('Mandatory, accepts multiple IDs separated by "|". You need to enter the ID of a Compliance Package Items, you can find them at Compliance Mgt / Compliance Package Items')
			],
		];
	}

	public function getReportsConfig()
	{
		return [
			'finder' => [
				'options' => [
					'contain' => [
						'Tag',
						'CustomFieldValue',
						'ComplianceManagement' => [
							'Owner',
							'ComplianceTreatmentStrategy',
							'Legal',
							'CustomFieldValue',
							'Project',
							'SecurityService',
							'SecurityPolicy',
							'Risk',
							'ThirdPartyRisk',
							'BusinessContinuity',
							'ComplianceAnalysisFinding',
							'ComplianceException',
							'Asset',
							'CompliancePackageItem' => [
								'CompliancePackage' => [
									'CompliancePackageRegulator'
								]
							]
						],
						'CompliancePackageRegulator' => [
							'Legal',
							'CompliancePackage',
							'Owner',
							'OwnerGroup',
						],
						'CompliancePackageItem' => [
							'CompliancePackage',
							'ComplianceManagement',
							'ComplianceFinding',
							'ComplianceAuditSetting'
						],
						'Owner',
						'OwnerGroup',
						'Collaborator',
						'CollaboratorGroup'
					]
				]
			],
			'table' => [
				'model' => [
					'ComplianceManagement', 'CompliancePackageRegulator', 'CompliancePackageItem'
				]
			],
		];
	}

	/*
	 * Type of statuses
	 */
	 public static function statuses($value = null) {
		$options = array(
			self::STATUS_CLOSED => __('Closed'),
			self::STATUS_OPEN => __('Open')
		);
		return parent::enum($value, $options);
	}
	const STATUS_CLOSED = 0;
	const STATUS_OPEN = 1;

	public function getStatuses() {
		return self::statuses();
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
			],
			'compliance_analysis_finding_expiration_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => -1,
				'label' => __('Compliance Analysis Finding Expiring in (-1 day)'),
				'description' => __('Notifies 1 day before a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => -5,
				'label' => __('Compliance Analysis Finding Expiring in (-5 days)'),
				'description' => __('Notifies 5 days before a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => -10,
				'label' => __('Compliance Analysis Finding Expiring in (-10 days)'),
				'description' => __('Notifies 10 days before a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => -20,
				'label' => __('Compliance Analysis Finding Expiring in (-20 days)'),
				'description' => __('Notifies 20 days before a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => -30,
				'label' => __('Compliance Analysis Finding Expiring in (-30 days)'),
				'description' => __('Notifies 30 days before a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => 1,
				'label' => __('Compliance Analysis Finding Expiring in (+1 day)'),
				'description' => __('Notifies 1 day after a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => 5,
				'label' => __('Compliance Analysis Finding Expiring in (+5 days)'),
				'description' => __('Notifies 5 days after a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => 10,
				'label' => __('Compliance Analysis Finding Expiring in (+10 days)'),
				'description' => __('Notifies 10 days after a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => 20,
				'label' => __('Compliance Analysis Finding Expiring in (+20 days)'),
				'description' => __('Notifies 20 days after a Compliance Analysis Finding expires')
			],
			'compliance_analysis_finding_expiration_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ComplianceAnalysisFindingExpiration',
				'days' => 30,
				'label' => __('Compliance Analysis Finding Expiring in (+30 days)'),
				'description' => __('Notifies 30 days after a Compliance Analysis Finding expires')
			]
		]);

		return $config;
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'CompliancePackageItem',
			]
		];
	}

	public function getObjectStatusConfig() {
        return [
            'open' => [
            	'title' => __('Open'),
                'callback' => [$this, 'statusOpen'],
                'type' => 'success',
                'storageSelf' => false,
            ],
            'closed' => [
            	'title' => __('Closed'),
                'callback' => [$this, 'statusClosed'],
                'type' => 'success',
                'storageSelf' => false,
            ],
            'expired' => [
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'trigger' => [
                	[
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.compliance_analysis_finding_expired'
                    ],
                ],
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusExpired($conditions = null)
    {
        $data = $this->find('count', [
        	'conditions' => [
        		'ComplianceAnalysisFinding.id' => $this->id,
	            'ComplianceAnalysisFinding.status !=' => self::STATUS_CLOSED,
				'DATE(ComplianceAnalysisFinding.due_date) < DATE(NOW())'
			],
			'recursive' => -1
        ]);

        return (bool) $data;
    }

    public function statusOpen() {
    	$data = $this->find('count', [
    		'conditions' => [
    			'ComplianceAnalysisFinding.id' => $this->id,
    			'ComplianceAnalysisFinding.status' => self::STATUS_OPEN
			]
		]);

		return (boolean) $data;
    }

    public function statusClosed() {
    	return !$this->statusOpen();
    }

    public function getPackageItemOptions($regulators = null)
    {
    	if (empty($regulators)) {
    		return [];
    	}

    	$data = ClassRegistry::init('CompliancePackageRegulator')->find('all', [
			'conditions' => [
				'CompliancePackageRegulator.id' => $regulators,
			],
			'fields' => [
				'CompliancePackageRegulator.id', 'CompliancePackageRegulator.name'
			],
			'contain' => [
				'CompliancePackage' => [
					'fields' => [
						'CompliancePackage.id', 'CompliancePackage.compliance_package_regulator_id'
					],
					'CompliancePackageItem' => [
						'fields' => [
							'CompliancePackageItem.id', 'CompliancePackageItem.name', 'CompliancePackageItem.item_id', 'CompliancePackageItem.compliance_package_id'
						],
					]
				],
			]
		]);

		$packageItemOptions = [];

		foreach ($data as $key => $regulator) {
			$itemIds = Hash::combine(
				$regulator, 
				'CompliancePackage.{n}.CompliancePackageItem.{n}.id', 
				'CompliancePackage.{n}.CompliancePackageItem.{n}.item_id'
			);

			$names = Hash::combine(
				$regulator, 
				'CompliancePackage.{n}.CompliancePackageItem.{n}.id', 
				'CompliancePackage.{n}.CompliancePackageItem.{n}.name'
			);

			foreach ($itemIds as $key => $itemId) {
				$packageItemOptions[$regulator['CompliancePackageRegulator']['name']][$key] = "({$itemId}) {$names[$key]}";
			}
		}

		return $packageItemOptions;
    }

	/*public function beforeValidate($options = array()) {
		if (!$this->checkRelatedExists('BusinessUnit', $this->data['Asset']['business_unit_id'])) {
			$this->invalidate('business_unit_id', __('At least one of the selected items does not exist.'));
		}
	}*/

	public function afterAuditProperty($Model, $propertyName, $oldValue, $newValue) {
		$this->propertyChangeNotification($propertyName, $oldValue, $newValue, 'status', 'StatusChange', self::statuses());
	}
	
	public function validatePackageItems($check) {
		if (empty($this->data['ComplianceAnalysisFinding']['CompliancePackageItem']) || empty($this->data['ComplianceAnalysisFinding']['CompliancePackageRegulator'])) {
			return false;
		}
		
		$items = $this->CompliancePackageItem->find('list', [
			'conditions' => [
				'CompliancePackageItem.id' => $this->data['ComplianceAnalysisFinding']['CompliancePackageItem']
			],
			'fields' => [
				'CompliancePackageItem.id', 'CompliancePackage.compliance_package_regulator_id'
			],
			'contain' => ['CompliancePackage']
		]);

		foreach ($items as $regulatorId) {
			if (!in_array($regulatorId, $this->data['ComplianceAnalysisFinding']['CompliancePackageRegulator'])) {
				return false;
			}
		}

		return true;
	}

	public function beforeValidate($options = array()) {
		$ret = true;

		if (isset($this->data['ComplianceAnalysisFinding']['CompliancePackageRegulator'])) {
			$this->invalidateRelatedNotExist('CompliancePackageRegulator', 'CompliancePackageRegulator', $this->data['ComplianceAnalysisFinding']['CompliancePackageRegulator']);
		}

		if (isset($this->data['ComplianceAnalysisFinding']['CompliancePackageItem'])) {
			$this->invalidateRelatedNotExist('CompliancePackageItem', 'CompliancePackageItem', $this->data['ComplianceAnalysisFinding']['CompliancePackageItem']);
		}

		return $ret;
	}

	public function beforeSave($options = array()) {
        // $this->transformDataToHabtm(array('ThirdParty', 'CompliancePackageItem'));

        if (isset($this->data['CompliancePackageItem']['CompliancePackageItem'])) {
        	$items = (array) $this->data['CompliancePackageItem']['CompliancePackageItem'];
        	$items = array_filter($items);
	        $ret = $managementItems = $this->complianceIntegrityCheck($items);

	        $this->data['ComplianceManagement']['ComplianceManagement'] = $managementItems;
        }

        return true;
    }

    // list of related compliance items
    public function getComplianceManagements($items) {
    	return $this->ComplianceManagement->find('list', [
        	'conditions' => [
        		'ComplianceManagement.compliance_package_item_id' => $items
        	],
        	'fields' => ['id', 'compliance_package_item_id'],
        	'recursive' => -1
        ]);
    }

    /**
     * Adds a missing compliance rows into db.
     */
    public function complianceIntegrityCheck($items) {
    	$managementItems = $this->getComplianceManagements($items);

        $missingItems = array_diff($items, $managementItems);
        if (empty($missingItems)) {
        	return array_keys($managementItems);
        }

        // saves the new missing records in compliance managements table
        if (!$this->ComplianceManagement->addItem($missingItems)) {
        	return false;
        }

        return $this->complianceIntegrityCheck($items);
    }

	// public function afterSave($created, $options = array()) {
	// }

	// public function beforeDelete($cascade = true) {
	// }

	// public function afterDelete() {
	// }

	public function bindComplianceJoinModel() {
		$modelAssoc = $this->getAssociated('ComplianceManagement');
		$with = $modelAssoc['with'];

		$this->bindModel(array(
			'hasMany' => array($with)
		));

		return $with;
	}
	/**
	 * Get commonly accessed compliance data through Analysis Finding model.
	 * 
	 * @param  array  $ids Finding IDs.
	 */
	public function getCommonComplianceData($ids = array()) {
		$with = $this->bindComplianceJoinModel();

		$joinIds = $this->{$with}->find('list', array(
			'conditions' => array(
				$with . '.compliance_analysis_finding_id' => $ids
			),
			'fields' => array(
				$with . '.compliance_management_id',
				$with . '.compliance_management_id'
			),
			'recursive' => -1
		));
		
		$commonData = $this->ComplianceManagement->getCommonComplianceData($joinIds);

		return $commonData;	
	}

	public function getThirdParties() {
		return $this->ComplianceManagement->getThirdParties();
	}

	public function getList() {
		$data = $this->find('list', array(
			'order' => array('ComplianceAnalysisFinding.title' => 'ASC'),
		));

		return $data;
	}

	public function convertTagsImport($value) {
		if (!empty($value)) {
			App::uses('Tag', 'Model');
			return implode(Tag::VALUE_SEPARATOR, $value);
		}

		return false;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
