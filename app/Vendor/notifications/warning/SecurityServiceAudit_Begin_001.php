<?php
class SecurityServiceAudit_Begin_001 extends SecurityServiceAudit_Begin_Base {
	protected $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Security Service Audit Deadline (-10 days)');
		$this->description = __('Notifies 10 days before a scheduled Security Audit begins');

		$this->conditions = array(
			$this->model . '.planned_date' => date('Y-m-d', strtotime('+10 days'))
		);*/
	}
}
