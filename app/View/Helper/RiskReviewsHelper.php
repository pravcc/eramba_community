<?php
App::uses('AppHelper', 'View/Helper');
class RiskReviewsHelper extends AppHelper {
	public $helpers = array('NotificationSystem', 'Html', 'FieldData.FieldData', 'Reviews', 'Limitless.Alerts');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

}