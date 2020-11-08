<?php
class BusinessContinuity_Expiration extends BusinessContinuity_Expiration_Base {
	protected $reminderDays = -10;
	// public $internal = 'business_continuity_expiration';
	// public $model = 'BusinessContinuity';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Business Risk Upcoming Review');
		$this->description = __('Notifies 10 days before a Business Risk Review begins');

		$this->conditions = array(
			$this->model . '.review' => date('Y-m-d', strtotime('+10 days', strtotime(date('Y-m-d'))))
		);*/
	}
}
