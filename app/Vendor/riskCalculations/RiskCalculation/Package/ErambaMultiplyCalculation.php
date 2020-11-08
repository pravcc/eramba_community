<?php
namespace RiskCalculation\Package;
use RiskCalculation\Package\ErambaCalculation;

class ErambaMultiplyCalculation extends ErambaCalculation {

	public function __construct() {
		parent::__construct();

		$this->mathOperator = '*';
		
		$this->name = 'Eramba (multiplication)';
		$this->description = __('The formula for this calculation method is: The multiplication of all selected classifications multiplied by the summatory of all applicable liabilities magnifier. The second factor is voided if the liabilities magnifier equals zero.');
	}

}
