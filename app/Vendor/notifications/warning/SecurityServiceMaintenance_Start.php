<?php
class SecurityServiceMaintenance_Start extends SecurityServiceMaintenance_Start_Base {
	protected $reminderDays = -5;
	// public $internal = 'security_service_maintenance_start';
	// public $model = 'SecurityServiceMaintenance';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Security Maintenance About to Start');
		$this->description = __('Notifies 5 days before a security control maintenance starts');

		$this->conditions = array(
			$this->model . '.planned_date' => date('Y-m-d', strtotime('+5 days'))
		);*/
	}
}
