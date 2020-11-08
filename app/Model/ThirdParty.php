<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('UserFields', 'UserFields.Lib');

class ThirdParty extends AppModel
{
	const TYPE_REGULATORS = 3;
	
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
		'AdvancedQuery.AdvancedFinder',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description', 'third_party_type_id'
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
		'UserFields.UserFields' => [
			'fields' => [
				'Sponsor' => [
					'mandatory' => false
				]
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
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
		)
	);

	public $belongsTo = array(
		'ThirdPartyType'
	);

	public $hasMany = array(
		'ServiceContract',
		'ComplianceAudit',
		// 'SecurityIncident',
	);

	public $hasAndBelongsToMany = array(
		'ThirdPartyRisk',
		'Legal',
		'SecurityIncident'
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Third Parties');
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
				'description' => __(''),
				'inlineEdit' => true
			],
			'third_party_type_id' => [
				'label' => __('Type'),
				'editable' => true,
				'description' => __('INFORMATIVE: Select an applicable type for this Third Party. Note: if you plan to use this third party for compliance pacakges then select "Regulator"'),
				'inlineEdit' => true
			],
			'Sponsor' => $UserFields->getFieldDataEntityData($this, 'Sponsor', [
				'label' => __('Sponsor'),
				'description' => __('INFORMATIVE: Choose a representative for this third party'),
				'quickAdd' => true,
				'inlineEdit' => true
			]),
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('OPTIONAL: Describe the function of the third party within your GRC program'),
				'inlineEdit' => true
			],
			'Legal' => [
				'label' => __('Liabilities'),
				'editable' => true,
				'description' => __('OPTIONAL: Choose all applicable liabilites for this third party, this will affect the Risk Score for this Third Party'),
				'quickAdd' => true,
				'inlineEdit' => true
			],
			'ThirdPartyRisk' => [
				'label' => __('Third Party Risks'),
				'editable' => false,
			],
			'SecurityIncident' => [
				'label' => __('Security Incidents'),
				'editable' => false,
			],
			'ServiceContract' => [
				'label' => __('Service Contracts'),
				'editable' => false,
			],
			'ComplianceAudit' => [
				'label' => __('Compliance Audits'),
				'editable' => false,
			],
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Third Parties'),
			'pdf_file_name' => __('third_parties'),
			'csv_file_name' => __('third_parties'),
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
				->textField('name', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->selectField('third_party_type_id', [ClassRegistry::init('ThirdPartyType'), 'getList'], [
					'showDefault' => true
				])
				->userField('Sponsor', 'Sponsor', [
					'showDefault' => true
				])
				->multipleSelectField('Legal', [ClassRegistry::init('Legal'), 'getList'], [
					'showDefault' => true
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
			->group('ThirdParty', [
				'name' => __('Third Party')
			])
				->multipleSelectField('ThirdParty', [$this, 'getList'], [
					'label' => __('Third Party')
				]);

		return $advancedFilterConfig;
	}

	public function getSectionInfoConfig()
	{
		return [
			'description' => __('Third Parties are organisations with whom you collaborate and exchange relevant information and assets'),
			'map' => [
				'VendorAssessment',
				'SecurityIncident',
				'ServiceContract',
				'DataAsset',
				'ThirdPartyRisk', 
			]
		];
	}

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [],
			'seed' => [],
		];
	}

	public function beforeSave($options = array()){
		// $this->transformDataToHabtm(['Legal']);

		return true;
	}

	public function beforeFind($query) {
		parent::beforeFind(null);
		if (!isset($query['conditions'][$this->alias . '._hidden']) || $query['conditions'][$this->alias . '._hidden'] != 1) {
			$query['conditions'][$this->alias . '._hidden'] = 0;
		}
		else {
			$query['conditions'][$this->alias . '._hidden'] = array(0, 1);
		}

		return $query;
	}

	public function beforeDelete($cascade = true) {
		$ret = $this->deleteUselessRisk();
		$this->setRisksData();

		return $ret;
	}

	public function afterDelete() {
		$this->updateRiskScores();
	}

	public function getNotificationSystemConfig()
	{
		return parent::getNotificationSystemConfig();
	}

	/**
	 * Delete Third Party Risks that are associated with currently deleted Third Party only.
	 */
	private function deleteUselessRisk() {
		$data = $this->ThirdPartiesThirdPartyRisk->find('all', array(
			'conditions' => array(
				'ThirdPartiesThirdPartyRisk.third_party_id' => $this->id
			)
		));

		$ret = true;
		foreach ($data as $risk) {
			$count = $this->ThirdPartiesThirdPartyRisk->find('count', array(
				'conditions' => array(
					'ThirdPartiesThirdPartyRisk.third_party_risk_id' => $risk['ThirdPartiesThirdPartyRisk']['third_party_risk_id']
				)
			));

			if ($count == 1) {
				$ret &= $this->ThirdPartyRisk->delete($risk['ThirdPartiesThirdPartyRisk']['third_party_risk_id']);
			}
		}

		return $ret;
	}

	public function getThirdPartyTypes() {
		$data = $this->ThirdPartyType->find('list', array(
			'order' => array('ThirdPartyType.name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getLegals() {
		$data = $this->Legal->find('list', array(
			'order' => array('Legal.name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	/**
	 * Set Third Party Risk IDs to the model for afterDelete() risk score updates.
	 */
	public function setRisksData() {
		$data = $this->find('all', array(
			'conditions' => array(
				'ThirdParty.id' => $this->id
			),
			'contain' => array(
				'ThirdPartyRisk' => array(
					'fields' => array('id')
				)
			)
		));

		$this->TpRiskIds = array();
		foreach ($data as $tp) {
			foreach ($tp['ThirdPartyRisk'] as $tpr) {
				$this->TpRiskIds[] = $tpr['id'];
			}
		}
	}

	public function updateRiskScores() {
		$this->ThirdPartyRisk->calculateAndSaveRiskScoreById($this->TpRiskIds);
	}

	public function hasSectionIndex()
	{
		return true;
	}

}
