<?php
App::uses('AppModel', 'Model');
App::uses('RiskAppetiteThreshold', 'Model');
App::uses('RiskCalculation', 'Model');

class RiskAppetite extends AppModel {
	/**
	 * Required number of classification types to manage new Risk Appetites functionality.
	 */
	const REQUIRED_COUNT = 2;

	public $actsAs = array(
		'Containable',
	);

	public $hasOne = array(
		'RiskAppetiteThresholdDefault' => [
			'className' => 'RiskAppetiteThreshold',
			'foreignKey' => 'risk_appetite_id',
			'conditions' => [
				'RiskAppetiteThresholdDefault.type' => RiskAppetiteThreshold::TYPE_DEFAULT
			]
		]
	);

	public $hasMany = array(
		'RiskAppetiteThreshold' => [
			'className' => 'RiskAppetiteThreshold',
			'foreignKey' => 'risk_appetite_id',
			'conditions' => [
				'RiskAppetiteThreshold.type' => RiskAppetiteThreshold::TYPE_GENERAL
			]
		]
	);

	public $hasAndBelongsToMany = array(
		'RiskClassificationType'
	);

	public $validate = array(
		'method' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'You must choose one risk appetite method'
			),
			'inList' => [
				'rule' => ['inList', [
					self::TYPE_INTEGER,
					self::TYPE_THRESHOLD
				]],
				'message' => 'This method is not supported'
			]
		)
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'method' => [
				// 'type' => 'toggle',
				'label' => __('Method'),
				'editable' => true,
				// 'options' => ['RiskAppetite', 'methods'],
			],
			'RiskClassificationType' => [
				'label' => __('Risk Classification Types'),
				'editable' => true,
				'description' => __('Select two risk classification types which will be used to define risk thresholds.'),
				'options' => [$this, 'getClassificationTypes']
			],
			'risk_appetite' => [
				'label' => 'risk appetite',
				'editable' => true,
				'description' => __('Set the risk appetite value - this must be an integer value (1,10,17, Etc). All risks with a residual score higher than this value will show the status "Risk Above Appetite"')
			]

		];

		parent::__construct($id, $table, $ds);
	}

	public function getClassificationTypes() {
		return $this->RiskClassificationType->find('list', array(
			'fields' => array('id', 'name'),
			'recursive' => -1
		));
	}

	public function beforeValidate($options = array()) {
		// debug($this->data);
		if ($this->data['RiskAppetite']['method'] == RiskAppetite::TYPE_THRESHOLD) {
			// validate calculation method first
			if (!$this->_validateRiskCalculation()) {
				$this->invalidate('method', __('Risk Appetite method cannot be configured as Threshold as it is not compatible with Magerit Risk Calculation method which is used in your Risk section'));
			}

			$this->validator()->add('RiskClassificationType', 'count', array(
				'rule' => ['multiple', [
					'min' => self::REQUIRED_COUNT,
					'max' => self::REQUIRED_COUNT
				]],
				'message' => __('You have to enter exactly %d classification types', self::REQUIRED_COUNT)
			));

			$thresholds = [];
			if (!empty($this->data['RiskAppetiteThreshold'])) {
				foreach ($this->data['RiskAppetiteThreshold'] as $key => $threshold) {
					$extract = Hash::extract(
						$threshold,
						'RiskAppetiteThresholdClassification.{n}.risk_classification_id'
					);

					sort($extract);
					if (in_array($extract, $thresholds)) {
						$this->invalidate('threshold_'. $key, __('You have duplicated threshold using the same classifications'));

						return false;
					}

					$thresholds[] = $extract;
				}
			}
		}

		if ($this->data['RiskAppetite']['method'] == RiskAppetite::TYPE_INTEGER) {
			$this->RiskAppetiteThresholdDefault->validate = [];
			$this->RiskAppetiteThreshold->validate = [];
		}

		return true;
	}

	/**
	 * Validate if there is no Risk section using Magerit risk calculation as
	 * it's not compatible with Risk Appetite Threshold configuration.
	 * 
	 * @return bool True if there is no Magerit calculation configured, False otherwise
	 */
	protected function _validateRiskCalculation()
	{
		$checkModels = ['Risk', 'ThirdPartyRisk', 'BusinessContinuity'];

		$ret = $this->data['RiskAppetite']['method'] == self::TYPE_THRESHOLD;
		foreach ($checkModels as $model) {
			$Model = ClassRegistry::init($model);
			$method = $Model->Behaviors->RiskCalculationManager->getMethod($Model);

			$ret &= $method !== RiskCalculation::METHOD_MAGERIT;
		}
		
		return $ret;
	}

	public function beforeSave($options = array()){
		$ret = true;

		if (!empty($this->data[$this->alias][$this->primaryKey])) {
			$ret &= $this->RiskAppetiteThreshold->deleteAll([
				'RiskAppetiteThreshold.risk_appetite_id' => $this->data[$this->alias][$this->primaryKey]
			]);
		}

		// $this->transformDataToHabtm(['RiskClassificationType']);

		return $ret;
	}

	/**
	 * Get current Risk Appetite method.
	 * 
	 * @return int Risk Appetite method.
	 */
	public function getCurrentType() {
		$data = $this->find('first', [
			'fields' => [
				'RiskAppetite.method'
			],
			'recursive' => -1
		]);

		return (int) $data['RiskAppetite']['method'];
	}

	/**
	 * Available methods for Risk Appetite.
	 */
	 public static function methods($value = null) {
		$options = array(
			self::TYPE_INTEGER => __('Integer'),
			self::TYPE_THRESHOLD => __('Threshold')
		);
		return parent::enum($value, $options);
	}
	const TYPE_INTEGER = 0;
	const TYPE_THRESHOLD = 1;

	/**
	 * General descriptions for Risk Appetite methods.
	 * 
	 * @param  const $value  Which Appetite method.
	 * @return string        Description.
	 */
	public static function methodDescriptions($value) {
		$descriptions = [
			self::TYPE_INTEGER => __('TBD'),
			self::TYPE_THRESHOLD => __('TBD')
		];

		return $descriptions[$value];
	}

}
