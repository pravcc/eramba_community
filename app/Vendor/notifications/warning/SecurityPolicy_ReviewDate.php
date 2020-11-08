<?php
class SecurityPolicy_ReviewDate extends SecurityPolicy_ReviewDate_Base {
	protected $reminderDays = -10;
	// public $internal = 'security_policy_review_date';
	// public $model = 'SecurityPolicy';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Security Policy Upcoming Review');
		$this->description = __('Notifies 10 days before a scheduled Security Policy Review begins');

		$this->conditions = array(
			$this->model . '.next_review_date' => date('Y-m-d', strtotime('+10 days'))
		);*/
	}
}
