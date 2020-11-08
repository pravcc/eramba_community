<?php
class ComplianceFinding_Deadline_Base extends NotificationsBase {
	public $internal = 'compliance_finding_deadline';
	public $model = 'ComplianceFinding';
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
			$this->title = __('Compliance Finding Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Compliance Finding expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.deadline' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Compliance Finding Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Compliance Finding expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.deadline' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
