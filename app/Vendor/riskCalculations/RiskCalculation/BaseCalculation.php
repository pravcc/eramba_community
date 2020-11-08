<?php
namespace RiskCalculation;
use EvalMath;

class BaseCalculation {
	public $name;
	public $description;
	public $requirements;
	public $settings;
	public $conditions;
	public $allowedModels = array();
	public $calculationMath = '';
	public $otherData = array();

	//invalid calculation
	private $invalid = false;
	private $EvalMath;

	public function __construct() {
		
	}

	protected function setInvalid() {
		$this->invalid = true;
	}

	protected function isValid() {
		return !$this->invalid;
	}

	protected function m($math = "") {
		if (!($this->EvalMath instanceof EvalMath)) {
			$this->EvalMath = new EvalMath\EvalMath;
		}
		
		return $this->EvalMath->evaluate($math);
	}

	protected function setError(\Model $Model) {
		
	}
}