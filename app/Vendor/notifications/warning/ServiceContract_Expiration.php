<?php
class ServiceContract_Expiration extends ServiceContract_Expiration_Base {
	protected $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Security Contract About to Expire');
		$this->description = __('Notifies 10 days before a Security Contract expires');

		$this->conditions = array(
			$this->model . '.end' => date('Y-m-d', strtotime('+10 days', strtotime(date('Y-m-d'))))
		);*/
	}
}
