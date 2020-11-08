<?php
class Project_Inactivity extends InactivityBase {
	public $internal = 'project_inactivity';
	public $model = 'Project';

	public function __construct($options = array()) {
		parent::__construct($options);
	}

	public function parseData($item) {
		$ret = parent::parseData($item);
		$ret &= self::checkStatus($item);

		return $ret;
	}

	/**
	 * Checks if a project status is not completed for this notification.
	 * 
	 * @return bool True if project is not completed, false otherwise.
	 */
	public static function checkStatus($item) {
		return $item['Project']['project_status_id'] != PROJECT_STATUS_COMPLETED;
	}
}