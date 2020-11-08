<?php
class SecurityServiceMaintenance_Start_Base extends NotificationsBase {
	public $internal = 'security_service_maintenance_start';
	public $model = 'SecurityServiceMaintenance';
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
			$this->title = __('Security Service Maintenance Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a scheduled Security Maintenance begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.planned_date' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Security Service Maintenance Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a scheduled Security Maintenance begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.planned_date' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
