<?php
App::uses('Project_Inactivity', 'Vendor/notifications/awareness');
class ProjectAchievement_Inactivity extends InactivityBase {
	public $internal = 'project_task_inactivity';
	public $model = 'ProjectAchievement';

	public function __construct($options = array()) {
		parent::__construct($options);

		$this->contain['Project'] = ['fields' => ['Project.project_status_id']];
	}

	public function parseData($item) {
		$ret = parent::parseData($item);
		$ret &= $item['ProjectAchievement']['completion'] < 100;
		$ret &= $item['ProjectAchievement']['expired'] != ITEM_STATUS_EXPIRED;
		$ret &= Project_Inactivity::checkStatus($item);

		return $ret;
	}
}