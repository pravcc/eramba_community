<?php
class BusinessContinuity_Expiration_Base extends NotificationsBase {
	public $internal = 'business_continuity_expiration';
	public $model = 'BusinessContinuity';
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
			$this->title = __('Business Risk Upcoming Review (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Business Risk Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.review' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Business Risk Review (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Business Risk Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.review' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
