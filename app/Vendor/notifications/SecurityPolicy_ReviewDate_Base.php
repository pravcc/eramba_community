<?php
class SecurityPolicy_ReviewDate_Base extends NotificationsBase {
	public $internal = 'security_policy_review_date';
	public $model = 'SecurityPolicy';
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
			$this->title = __('Security Policy Upcoming Review (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Security Policy Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.next_review_date' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Security Policy Review (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Security Policy Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.next_review_date' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
