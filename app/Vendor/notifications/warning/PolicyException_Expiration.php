<?php
class PolicyException_Expiration extends PolicyException_Expiration_Base {
	protected $reminderDays = -10;
	// public $internal = 'policy_exception_expiration';
	// public $model = 'PolicyException';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Policy Exception about to expire');
		$this->description = __('Notifies 10 days before a Policy Exception expires');

		$this->conditions = array(
			$this->model . '.expiration' => date('Y-m-d', strtotime('+10 days', strtotime(date('Y-m-d'))))
		);*/
	}
}
