<?php
class ProjectAchievement_Deadline_Base extends NotificationsBase {
	public $internal = 'project_achievement_deadline';
	public $model = 'ProjectAchievement';
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
			$this->title = __('Project Task Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Project Task expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.date' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Project Task Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Project Task expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.date' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}

		// append a general condition to not include completed tasks - having 100% completion.
		$this->conditions[$this->model . '.completion <'] = 100;
	}

	/**
	 * Custom handler for empty data returned by this model due to $virtualFields configuration.
	 */
	public function parseData($item) {
		if (!empty($item['ProjectAchievement']['id'])) {
			return true;
		}

		return false;
	}
}
