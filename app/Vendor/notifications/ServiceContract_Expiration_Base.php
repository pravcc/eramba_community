<?php
class ServiceContract_Expiration_Base extends NotificationsBase {
	public $internal = 'service_contract_expiration';
	public $model = 'ServiceContract';
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
			$this->title = __('Security Contract Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Security Contract expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.end' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Security Service Audit Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Security Contract expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.end' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
