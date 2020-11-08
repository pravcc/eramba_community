<?php
App::uses('DashboardAppModel', 'Dashboard.Model');

class DashboardKpiThreshold extends DashboardAppModel {
	public $useTable = 'kpi_thresholds';

	public $actsAs = array(
		'Containable',
	);

	public $belongsTo = array(
		'DashboardKpi' => [
			'className' => 'Dashboard.DashboardKpi',
			'foreignKey' => 'kpi_id'
		]
	);

	public $validate = [
		'title' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be left blank'
			]
		],
		'description' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be left blank'
			]
		],
		'color' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'You must choose a color'
			)
		),
		'type' => array(
			'inList' => [
				'rule' => ['inList', [
					self::TYPE_RANGE,
					self::TYPE_CHANGE
				]],
				'message' => 'This type is not supported'
			]
		)
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'description' => __('Give this threshold a title, this will later be shown on the KPI'),
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('Provide a description for this threshold'),
			],
			'color' => [
				'label' => __('Color'),
				'editable' => true,
				'description' => __('Choose a colour for this threshold, we\'ll highliht the KPI with this colour if the condition matches')
			],
			'type' => [
				'label' => __('Type'),
				'editable' => true,
				'options' => ['DashboardKpiThreshold', 'types']
			],
			'min' => [
				'label' => __('Min'),
				'editable' => true,
				'description' => __('Min threshold value - this condition sets the minimum value needed to trigger this treshold. This value can be 0 if you want to match any KPI.')
			],
			'max' => [
				'label' => __('Max'),
				'editable' => true,
				'description' => __('Max threshold value - this condition sets the maximum value needed to trigger this treshold')
			],
			'percentage' => [
				'label' => __('Percentage'),
				'editable' => true,
				'description' => __('Percentage threshold in absolute terms, we will trigger any variation greater than this value. How we calculate this? Assuming the old value is X and new value is Y the math we will use is:<br><br> Step 1: Calculate the difference in between them: Z=X-Y<br>Step 2: Calculate the percentage variation: Percentage=Z/X*100')
			],
		];

		parent::__construct($id, $table, $ds);
	}

	public function beforeValidate($options = array()) {
		$this->validator()->remove('min');
		$this->validator()->remove('max');
		$this->validator()->remove('percentage');
		
		if ($this->data[$this->alias]['type'] == self::TYPE_RANGE) {
			$this->validator()->add('min', 'comparison', array(
				'rule' => ['comparison', '>=', 0],
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter number.'
			));

			$this->validator()->add('max', 'comparison', array(
				'rule' => ['comparison', '>=', 0],
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter number.'
			));
		}

		if ($this->data[$this->alias]['type'] == self::TYPE_CHANGE) {
			$this->validator()->add('percentage', 'range', array(
				'rule' => ['range', -1, 101],
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter percentage.'
			));
		}

	}

	public function beforeSave($options = array()) {
		return true;
	}

	/*
	 * Available types for appetite classifications.
	 */
	 public static function types($value = null) {
		$options = array(
			self::TYPE_RANGE => __('Range Min/Max'),
			self::TYPE_CHANGE => __('Change Percentage')
		);
		return parent::enum($value, $options);
	}
	const TYPE_RANGE = 0;
	const TYPE_CHANGE = 1;

}
