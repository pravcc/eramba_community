<?php
class RiskClassificationType extends AppModel {
	public $actsAs = array(
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name'
			)
		)
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true
		)
	);

	public $hasMany = array(
		'RiskClassification' => [
			'order' => 'RiskClassification.value DESC'
		]
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Risk Classification Types');

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
