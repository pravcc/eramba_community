<?php
App::uses('AppHelper', 'View/Helper');

class CronsHelper extends AppHelper {
	public $helpers = array('Html', 'Text');
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	/**
	 * Creates an execution time label in seconds.
	 */
	public function getExecutionTimeLabel($data, $options = array()) {
		if (!empty($data)) {
			return sprintf(__('%s seconds'), $data);
		}

		return false;
	}
}