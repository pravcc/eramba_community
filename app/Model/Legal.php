<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('UserFields', 'UserFields.Lib');
App::uses('NotificationSystem', 'NotificationSystem.Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');

class Legal extends AppModel {
	public $displayField = 'name';

	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description', 'risk_magnifier'
			)
		),
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'UserFields.UserFields' => [
			'fields' => ['LegalAdvisor']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'ImportTool.ImportTool',
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'description' => array(
		),
		'risk_magnifier' => array(
			'rule' => 'numeric',
			'allowEmpty' => true
		)
	);

	public $hasMany = [
		'Comment' => array(
			'className' => 'Comments.Comment',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Comment.model' => 'Legal'
			)
		),
	];

	public $hasAndBelongsToMany = array(
		'Asset',
		'BusinessUnit',
		'ThirdParty',
		// 'Project'
	);

	/**
	 * Description is in the AppModel
	 */
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

		$this->label = __('Legal');
		$this->_group = 'organization';

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('Give a name to this Liability. For example "Contractual Liabilities, Legal Liabilities, Customer Liabilities, Etc'),
				'inlineEdit' => true,
			],
			'description' => [
				// 'type' => 'editor',
				'label' => __('Description'),
				'editable' => true,
				'description' => __('Give a brief description of what this liability involves.'),
				'inlineEdit' => true,
			],
			'risk_magnifier' => [
				'label' => __('Risk Magnifier'),
				'editable' => true,
				'description' => __('OPTIONAL: If you are using "eramba" Risk Calculation, this value will automatically increase (if you set values over "1") or decrease (if you choose values under "1") Risk scores for any Risk that has in some way this liability asociated.<br><br> Remember that Assets (Asset Management / Asset Identification), Third Parties (Organisation / Third Parties) and Business Units (Organisation / Bussiness Units) can be linked with these liabilities.'),
				'inlineEdit' => true,
				'renderHelper' => ['Legals', 'riskMagnifierField']
			],
			'LegalAdvisor' => $UserFields->getFieldDataEntityData($this, 'LegalAdvisor', [
				'label' => __('Legal Advisor'), 
				'description' => __('INFORMATIVE: Choose one representative for this liability, whatever you input here will not be used for notifications, etc. This field is simply informative.'),
				'dependency' => true,
				'inlineEdit' => true,
				'quickAdd' => true
			]),
			'Asset' => [
				'label' => __('Assets'),
				'editable' => false,
			],
			'BusinessUnit' => [
				'label' => __('Business Units'),
				'editable' => false,
			],
			'ThirdParty' => [
				'label' => __('Third Parties'),
				'editable' => false,
			],
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Legals'),
			'pdf_file_name' => __('legals'),
			'csv_file_name' => __('legals'),
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
				->textField('name', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->userField('LegalAdvisor', 'LegalAdvisor', [
					'showDefault' => true
				])
				->numberField('risk_magnifier', [
					'showDefault' => true
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getImportToolConfig()
	{
		return [
			'Legal.name' => [
				'name' => $this->getFieldCollection()->get('name')->getLabel(),
				'headerTooltip' => $this->getFieldCollection()->get('name')->getDescription(),
			],
			'Legal.description' => [
				'name' => $this->getFieldCollection()->get('description')->getLabel(),
				'headerTooltip' => $this->getFieldCollection()->get('description')->getDescription(),
			],
			'Legal.risk_magnifier' => [
				'name' => $this->getFieldCollection()->get('risk_magnifier')->getLabel(),
				'headerTooltip' => $this->getFieldCollection()->get('risk_magnifier')->getDescription(),
			],
			'Legal.LegalAdvisor' => UserFields::getImportArgsFieldData('LegalAdvisor', [
				'name' => $this->getFieldCollection()->get('LegalAdvisor')->getLabel()
			], true),
		];
	}

	public function getSectionInfoConfig()
	{
		return [
			'description' => '',
			'map' => [
				'Asset' => [
					'Risk',
					'ThirdPartyRisk',
				],
				'ThirdParty',
				'BusinessUnit',
			]
		];
	}

	public function getNotificationSystemConfig()
	{
		return parent::getNotificationSystemConfig();
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [],
			'seed' => [],
		];
	}


	public function getReportsConfig()
    {
		return [
			'chart' => [
				1 => [
					'title' => __('Legal and related Objects'),
					'description' => __('This tree shows the legal and its asociated assets, third parties and projects.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'className' => 'RelatedObjectsChart',
					'params' => [
						'assoc' => [
							'Asset',
							'ThirdParty',
							'BusinessUnit',
						],
					]
				],
			]
		];
	}

	public function afterSave($created, $options = array()) {
		if (!$created) {
			$this->setRisksData();
			$this->updateRiskScores();
		}
	}

	public function beforeDelete($cascade = true) {
		$this->setRisksData();
		return true;
	}

	public function afterDelete() {
		$this->updateRiskScores();
	}

	private function setRisksData() {
		$data = $this->find('first', array(
			'conditions' => array(
				'Legal.id' => $this->id,
			),
			'contain' => array(
				'BusinessUnit',
				'Asset',
				'ThirdParty'
			),
			'softDelete' => false
		));

		foreach ($data['BusinessUnit'] as $bu) {
			$this->BusinessUnit->id = $bu['id'];
			$this->BusinessUnit->setAssociatedBusinessContinuities();
		}

		foreach ($data['Asset'] as $asset) {
			$this->Asset->id = $asset['id'];
			$this->Asset->setRisksData();
		}

		foreach ($data['ThirdParty'] as $tp) {
			$this->ThirdParty->id = $tp['id'];
			$this->ThirdParty->setRisksData();
		}
	}

	private function updateRiskScores() {
		$this->BusinessUnit->updateRiskScores();
		$this->Asset->updateRiskScores();
		$this->ThirdParty->updateRiskScores();
	}

	public function getLegalAdvisors() {
		$data = $this->LegalAdvisor->find('list', array(
			'fields' => array('LegalAdvisor.id', 'LegalAdvisor.full_name'),
			'order' => array('LegalAdvisor.full_name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function isUsedInRisks($id)
	{
		// assets
		$assetIds = $this->AssetsLegal->find('list', [
			'conditions' => [
				'AssetsLegal.legal_id' => $id
			],
			'fields' => [
				'asset_id'
			],
			'recursive' => -1
		]);

		$assetRisks = $this->Asset->AssetsRisk->find('count', [
			'conditions' => [
				'AssetsRisk.asset_id' => $assetIds
			],
			'recursive' => -1
		]);

		// third parties
		$thirdPartyIds = $this->LegalsThirdParty->find('list', [
			'conditions' => [
				'LegalsThirdParty.legal_id' => $id
			],
			'fields' => [
				'third_party_id'
			],
			'recursive' => -1
		]);

		$thirdPartyRisks = $this->ThirdParty->ThirdPartiesThirdPartyRisk->find('count', [
			'conditions' => [
				'ThirdPartiesThirdPartyRisk.third_party_id' => $thirdPartyIds
			],
			'recursive' => -1
		]);

		//business units
		$businessUnitIds = $this->BusinessUnitsLegal->find('list', [
			'conditions' => [
				'BusinessUnitsLegal.legal_id' => $id
			],
			'fields' => [
				'business_unit_id'
			],
			'recursive' => -1
		]);

		$businessContinuities = $this->BusinessUnit->BusinessContinuitiesBusinessUnit->find('count', [
			'conditions' => [
				'BusinessContinuitiesBusinessUnit.business_unit_id' => $businessUnitIds
			],
			'recursive' => -1
		]);

		return $assetRisks + $thirdPartyRisks + $businessContinuities;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
