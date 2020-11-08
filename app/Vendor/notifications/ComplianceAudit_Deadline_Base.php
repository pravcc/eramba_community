<?php
class ComplianceAudit_Deadline_Base extends NotificationsBase {
	public $internal = 'compliance_audit_deadline';
	public $model = 'ComplianceAudit';
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
			$this->title = __('Third Party Audit Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a scheduled Third Party Audit begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.end_date' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Third Party Audit Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a scheduled Third Party Audit begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.end_date' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
