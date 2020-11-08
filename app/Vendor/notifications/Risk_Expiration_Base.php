<?php
class Risk_Expiration_Base extends NotificationsBase {
	public $internal = 'risk_expiration';
	public $model = 'Risk';
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
			$this->title = __('Asset Risk Upcoming Review (-%s)', $daysLabel);
			$this->description = __('Notifies %s before an Asset Risk Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.review' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Asset Risk Review (+%s)', $daysLabel);
			$this->description = __('Notifies %s after an Asset Risk Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.review' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
