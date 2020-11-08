<?php
App::uses('ComplianceAnalysisFinding', 'Model');

class ComplianceAnalysisFinding_Expiration_Base extends NotificationsBase {
	public $internal = 'compliance_analysis_finding_expiration';
	public $model = 'ComplianceAnalysisFinding';
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
			$this->title = __('Compliance Analysis Finding Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a Compliance Analysis Finding expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.due_date' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Compliance Analysis Finding Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a Compliance Analysis Finding expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.due_date' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}

		// append a general status condition to apply
		$this->conditions[$this->model . '.status !='] = ComplianceAnalysisFinding::STATUS_CLOSED;
	}
}
