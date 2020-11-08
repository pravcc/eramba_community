<?php
class RiskException_Expiration extends RiskException_Expiration_Base {
	protected $reminderDays = -10;
	// public $internal = 'risk_exception_expiration';
	// public $model = 'RiskException';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Risk Exception about to expire');
		$this->description = __('Notifies 10 days before a Risk Exception expires');

		$this->conditions = array(
			$this->model . '.expiration' => date('Y-m-d', strtotime('+10 days', strtotime(date('Y-m-d'))))
		);*/
	}
}
