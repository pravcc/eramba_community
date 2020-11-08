<?php
class RiskCalculationValue extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'RiskCalculation'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Risk Calculation Value');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'name' => [
				'label' => __('New Classification Type'),
				'editable' => true,
				'description' => __('If you havent created a Classification type before, you will need to create one. Examples are "Likelihood", "Impact", Etc')
			]
		];

		parent::__construct($id, $table, $ds);
	}
}
