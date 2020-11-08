<?php
class _ObjectReminder extends NotificationsBase {
	public $filename = '_ObjectReminder.php';
	public $internal = 'object_reminder';
	public $model = null;
	public $customEmailTemplate = true;

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Object Reminder');
		$this->description = __('Used to remind users about objects');
	}

	public function parseData($data) {
		return true;
	}
}
