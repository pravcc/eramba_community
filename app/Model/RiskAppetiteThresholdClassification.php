<?php
class RiskAppetiteThresholdClassification extends AppModel {
	public $useTable = 'risk_appetite_threshold_risk_classifications';
	
	public $actsAs = array(
		'Containable',
	);

	public $belongsto = array(
		'RiskAppetiteThreshold',
		'RiskClassification'
	);

	public $validate = [
		'risk_classification_id' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be left blank'
			]
		]
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'risk_classification_id' => array(
				'label' => __('Classification'),
				'editable' => true,
				'type' => 'select',
				'options' => [],
				// 'options' => [
				// 	'callable' => [$this, 'getClassifications'],
				// 	'passParams' => true
				// ],
				'description' => __('Select one classification for each risk classification type. The combination of these two categories defines a risk threshold.'),
				'empty' => __('Choose one ...')
			)
		];

		parent::__construct($id, $table, $ds);
	}

	/**
	 * Get options for conditional fields.
	 */
	public function getClassifications($type) {
		$classifications = ClassRegistry::init('RiskClassification')->find('all', [
			'conditions' => [
				'RiskClassification.risk_classification_type_id' => $type
			],
			'fields' => [
				'RiskClassification.id',
				'RiskClassification.name',
				'RiskClassification.criteria',
				'RiskClassification.value',
				'RiskClassificationType.name'
			],
			'recursive' => 0
		]);

        $options = array();
        foreach ($classifications as $item) {
             $options[$item['RiskClassification']['id']] = sprintf(
             	'[%s] %s',
             	$item['RiskClassificationType']['name'],
             	$item['RiskClassification']['name']
             );
        }

        return $options;
	}

	


}
