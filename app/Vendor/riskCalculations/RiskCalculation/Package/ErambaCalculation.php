<?php
namespace RiskCalculation\Package;
use RiskCalculation\BaseCalculation;

class ErambaCalculation extends BaseCalculation {
	public $allowedModels = array('Risk', 'ThirdPartyRisk', 'BusinessContinuity');
	public $mathOperator = '+';

	public function __construct() {
		parent::__construct();

		$this->name = 'Eramba';
		$this->description = __('The formula for this calculation method is: The summatory of all classifications multiplied by the summatory of all applicable liabilities magnifier. The second factor is voided if the liabilities magnifier equals zero.');
		$this->settings = array();
		$this->conditions = array();
	}

	public function calculate(\Model $Model, $options, $calculationValues = array()) {
		$this->otherData = array(
			'classificationsSecondPartMath' => ''
		);

		$this->calculationMath = '';
		//debug(method_exists($Model, 'ErambaCalculation'));

		// get section specific data for calculation
		$vals = array_values($options);
		// debug($options);
		if (!method_exists($Model, 'ErambaCalculation')) {
			return false;
		}
		if (empty($vals[0])) {
			$vals[0] = array();
		}
		if (empty($vals[1])) {
			$vals[1] = array();
		}
		
		$options = $Model->ErambaCalculation($vals[0], $vals[1]);
		if ($options == 0) {
			$this->calculationMath = '-';
			return 0;
		}

		$vals = array_values($options);

		$classifications = $options[0];
		$magnifierData = $options[1];


		$classification_sum = ($this->mathOperator == '+') ? 0 : 1;
		$classification_values = array();
		foreach ( $classifications as $classification ) {
			if ($this->mathOperator == '+') {
				$classification_sum += $classification['RiskClassification']['value'];
			}
			else {
				$classification_sum *= $classification['RiskClassification']['value'];
			}
			
			$classification_values[] = $classification['RiskClassification']['value'];
		}
		
		$magnifier_sum = 0;
		$magnifier_values = array();
		foreach ( $magnifierData as $asset ) {
			foreach ($asset['Legal'] as $legal) {
				$magnifier_sum += $legal['risk_magnifier'];
				$magnifier_values[] = $legal['risk_magnifier'];
			}
		}

		$classification_math = '0';
		if (!empty($classification_values)) {
			$classification_math = implode(" {$this->mathOperator} ", $classification_values);
		}

		if ( $magnifier_sum ) {
			$result = $classification_sum * $magnifier_sum;

			$this->calculationMath = sprintf(
				'(%s) x (%s) = %s',
				$classification_math,
				(!empty($magnifier_values) ? (implode(' + ', $magnifier_values)) : '0'),
				$result
			);

			// debug($this->calculationMath);

			$this->otherData['classificationsSecondPartMath'] = $this->calculationMath;

			return $result;
		}

		$this->calculationMath = sprintf('%s = %s', $classification_math, $classification_sum);

		$this->otherData['classificationsSecondPartMath'] = $this->calculationMath;

		return $classification_sum;
	}
}
