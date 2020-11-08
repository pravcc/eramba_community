<?php
class Risk_Expiration extends Risk_Expiration_Base {
	protected $reminderDays = -10;
	// public $internal = 'risk_expiration';
	// public $model = 'Risk';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Asset Risk Upcoming Review');
		$this->description = __('Notifies 10 days before a Risk Review begins');

		$this->conditions = array(
			$this->model . '.review' => date('Y-m-d', strtotime('+10 days', strtotime(date('Y-m-d'))))
		);*/
	}
}
