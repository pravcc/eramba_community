<?php
class Project_Deadline_Base extends NotificationsBase {
	public $internal = 'project_deadline';
	public $model = 'Project';
	protected $reminderDays = null;

	public function __construct($options = array()) {
		parent::__construct($options);

		if ($this->reminderDays === null) {
			return false;
		}

		$days = $this->reminderDays;

		// always positive number
		$absReminder = abs($days);
		$daysLabel = sprintf(__n('%s day', '%s days', $absReminder), $absReminder);
		
		if ($days < 0) {
			$this->title = __('Project Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Project expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.deadline' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Project Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Project expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.deadline' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}

		// append a general conditional rule to apply for projects having ongoing status
		$this->conditions[$this->model . '.project_status_id'] = PROJECT_STATUS_ONGOING;
	}
}
