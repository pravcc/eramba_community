<?php
class Asset_Expiration extends Asset_Expiration_Base {
	protected $reminderDays = -10;
	// public $internal = 'asset_expiration';
	// public $model = 'Asset';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		/*$this->title = __('Asset Upcoming Review');
		$this->description = __('Notifies 10 days before a Asset Review begins');

		$this->conditions = array(
			$this->model . '.review' => date('Y-m-d', strtotime('+10 days', strtotime(date('Y-m-d'))))
		);*/
	}
}
