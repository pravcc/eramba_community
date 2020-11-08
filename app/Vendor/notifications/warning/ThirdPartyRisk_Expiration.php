<?php
class ThirdPartyRisk_Expiration extends ThirdPartyRisk_Expiration_Base {
	protected $reminderDays = -10;
	// public $internal = 'third_party_risk_expiration';
	// public $model = 'ThirdPartyRisk';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Third Party Risk Upcoming Review');
		$this->description = __('Notifies 10 days before a Third Party Risk Review begins');

		$this->conditions = array(
			$this->model . '.review' => date('Y-m-d', strtotime('+10 days', strtotime(date('Y-m-d'))))
		);*/
	}
}
