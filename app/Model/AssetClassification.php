<?php
App::uses('AppModel', 'Model');

class AssetClassification extends AppModel
{
	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'criteria'
			)
		),
		'AuditLog.Auditable',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters'
	);

	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'workflow' => false,
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'criteria' => array(
		),
		'value' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Please enter a number'
			)
		),
	);

	public $belongsTo = array(
		'AssetClassificationType' => array(
			'counterCache' => true
		)
	);

	public $hasAndBelongsToMany = array(
		'Asset'
	);

	public $hasMany = array(
	);

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Asset Classifications');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'asset_classification_type_id' => [
				'label' => __('Classification Type'),
				'editable' => true,
				'description' => __('Select from this drop down an existing classification type or create a new one with the field below.'),
				'empty' => __('Choose one or create new below'),
				'renderHelper' => ['AssetClassifications', 'assetClassificationTypeField'],
			],
			'name' => [
				'label' => __('Classification Options'),
				'editable' => true,
				'description' => __('For each classification type, you will need to proivde options. Examples could be "High", "Low", etc.'),
			],
			'criteria' => [
				'label' => __('Criteria'),
				'editable' => true,
				'description' => __('Define a criteria for this classification option.'),
				'validates' => [
					'mandatory' => false
				]
			],
			'value' => [
				'label' => __('Value'),
				'editable' => true,
				'description' => __('Certain risk calculation methods (Magerit, Allegro, Etc) require you to classify your assets in numerical values, for that reason you need to provide a value for this classification.'),
				'renderHelper' => ['AssetClassifications', 'valueField']
			],
		];
		
		$this->advancedFilterSettings = array(
			'pdf_title' => __('Asset Classifications'),
			'pdf_file_name' => __('asset-classifications'),
			'csv_file_name' => __('asset-classifications'),
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
				->multipleSelectField('asset_classification_type_id', [$this, 'getAssetClassificationTypes'], [
					'label' => __('Classification Type'),
					'showDefault' => true
				])
				->textField('name', [
					'label' => __('Name'),
					'showDefault' => true
				])
				->textField('criteria', [
					'showDefault' => true
				])
				->textField('value', [
					'showDefault' => true
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function beforeSave($options = array()) {
		// $this->transformDataToHabtm(array('Asset'));

		if (isset($this->data['AssetClassification']['value']) && is_numeric($this->data['AssetClassification']['value'])) {
			$this->data['AssetClassification']['value'] = CakeNumber::precision($this->data['AssetClassification']['value'], 2);
		}

		return true;
	}

	public function afterDelete() {
		$data = $this->AssetClassificationType->find('list', array(
			'conditions' => array(
				'AssetClassificationType.asset_classification_count' => 0
			),
			'fields' => array('id'),
			'recursive' => -1
		));

		$d = $this->AssetClassificationType->deleteAll(array(
			'AssetClassificationType.id' => $data
		));
	}

	public function getRelatedRisks($classificationId) {
		$data = $this->find('all', array(
			'conditions' => array(
				'AssetClassification.id' => $classificationId
			),
			'contain' => array(
				'Asset' => array(
					'fields' => array('id'),
					'Risk' => array(
						'fields' => array('id')
					)
				)
			)
		));

		$riskIds = array();
		foreach ($data as $ac) {
			foreach ($ac['Asset'] as $asset) {
				foreach ($asset['Risk'] as $risk) {
					$riskIds[] = $risk['id'];
				}
			}
		}

		return array(
			'riskIds' => array_unique($riskIds)
		);
	}

	public function isUsed($classificationId) {
		$related = $this->getRelatedRisks($classificationId);
		if (!empty($related['riskIds'])) {
			return true;
		}
		return false;
	}

	public function getAssetClassificationTypes() {
		return $this->AssetClassificationType->getList();
	}
}
