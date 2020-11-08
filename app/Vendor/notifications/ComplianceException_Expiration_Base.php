<?php
class ComplianceException_Expiration_Base extends NotificationsBase {
	public $internal = 'compliance_exception_expiration';
	public $model = 'ComplianceException';
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
			$this->title = __('Compliance Exception Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Compliance Exception expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.expiration' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Compliance Exception Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Compliance Exception expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.expiration' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}

		// append a general status condition to apply
		$this->conditions[$this->model . '.status !='] = COMPLIANCE_EXCEPTION_CLOSED;
	}
}
