<?php
App::uses('AppModel', 'Model');
App::uses('RiskAppetite', 'Model');

class RiskAppetiteThreshold extends AppModel {
	public $actsAs = array(
		'Containable',
	);

	public $belongsto = array(
		'RiskAppetite'
	);

	public $hasMany = array(
		'RiskAppetiteThresholdClassification'
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
			// 'notEmpty' => array(
			// 	'rule' => 'notBlank',
			// 	'required' => true,
			// 	'message' => 'You must choose a type'
			// ),
			'inList' => [
				'rule' => ['inList', [
					self::TYPE_DEFAULT,
					self::TYPE_GENERAL
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
				'description' => __('Give this threshold a title, this will later be shown when the risk is created if the combination defined above is selected.'),
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('Provide a description for this threshold.'),
			],
			'color' => [
				'type' => 'text',
				'label' => __('Color'),
				'editable' => true,
				'description' => __('Choose a colour for this threshold.')
			],
			'type' => [
				'label' => __('Type'),
				'editable' => true,
				'options' => ['RiskAppetiteThreshold', 'types']
			]
		];

		parent::__construct($id, $table, $ds);
	}

	public function beforeSave($options = array()) {
		return true;
	}

	/**
	 * Get the query params for finding a threshold based on risk classifications.
	 * 
	 * @return array Query parameters.
	 */
	public function getThresholdQuery($riskClassificationId) {
		return [
			'conditions' => [
				'RiskAppetiteThresholdClassification.risk_classification_id' => $riskClassificationId,
				'RiskAppetiteThreshold.type' => self::TYPE_GENERAL
			],
			'fields' => [
				'RiskAppetiteThreshold.*'
			],
			'having' => array('COUNT(RiskAppetiteThresholdClassification.id)' => RiskAppetite::REQUIRED_COUNT),
			'group' => array('RiskAppetiteThreshold.id'),
			'joins' => [
				[
					'table' => 'risk_appetite_thresholds',
					'alias' => 'RiskAppetiteThreshold',
					'type' => 'LEFT',
					'conditions' => [
						'RiskAppetiteThresholdClassification.risk_appetite_threshold_id = RiskAppetiteThreshold.id'
					]
				]
			],
			'recursive' => -1
		];
	}

	/**
	 * Check if threshold for selected classifications exists.
	 * 
	 * @param  array   $riskClassificationId Risk classification IDs
	 * @return boolean                       True if exists, False otherwise
	 */
	public function hasThreshold($riskClassificationId) {
		return $this->RiskAppetiteThresholdClassification->find('count', $this->getThresholdQuery($riskClassificationId));
	}

	/**
	 * General method to read risk appetite threshold.
	 * 
	 * @param  array $riskClassificationId  Risk Classification IDs.
	 * @return array
	 */
	public function getThreshold($riskClassificationId) {
		if (!$this->hasThreshold($riskClassificationId)) {
			return $this->getDefault();
		}

		return $this->RiskAppetiteThresholdClassification->find('first', $this->getThresholdQuery($riskClassificationId));
	}

	/**
	 * Get default threshold configured.
	 * 
	 * @return array
	 */
	public function getDefault() {
		return $this->find('first', [
			'conditions' => [
				'type' => self::TYPE_DEFAULT
			],
			'recursive' => -1
		]);
	}

	/*
	 * Available colors for risk appetite threshold.
	 */
	 public static function colors($value = null) {
		$options = array(
			self::COLOR_DEFAULT => __('Grey'),
			self::COLOR_PRIMARY => __('Light Blue'),
			self::COLOR_SUCCESS => __('Green'),
			self::COLOR_INFO => __('Dark Blue'),
			self::COLOR_WARNING => __('Yellow'),
			self::COLOR_DANGER => __('Red')
		);
		return parent::enum($value, $options);
	}
	const COLOR_DEFAULT = 0;
	const COLOR_PRIMARY = 1;
	const COLOR_SUCCESS = 2;
	const COLOR_INFO = 3;
	const COLOR_WARNING = 4;
	const COLOR_DANGER = 5;

	/*
	 * Available types for appetite classifications.
	 */
	 public static function types($value = null) {
		$options = array(
			self::TYPE_DEFAULT => __('Default'),
			self::TYPE_GENERAL => __('General')
		);
		return parent::enum($value, $options);
	}
	const TYPE_DEFAULT = 0;
	const TYPE_GENERAL = 1;

	public function getList() {
        $data = $this->find('list', [
        	'fields' => [
        		$this->alias . '.title',
        		$this->alias . '.title',
        	],
            'order' => [
                $this->alias . '.' . $this->displayField => 'ASC'
            ],
            'group' => [
            	$this->alias . '.title'
            ]
        ]);

        return $data;
    }

}
