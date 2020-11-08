<?php
class StatusChange_Base extends NotificationsBase {
	public $internal = 'status_change';
	public $isDefaultType = true;
	public $customEmailTemplate = true;

	public function __construct($options = array()) {
		parent::__construct($options);

		$Model = ClassRegistry::init($this->model);
		
		$this->title = __('%s status update', $Model->label);
		$this->description = __('Triggers when a %s status is changed', $Model->label);
	}
}
