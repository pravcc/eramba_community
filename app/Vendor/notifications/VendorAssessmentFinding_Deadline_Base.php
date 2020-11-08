<?php
App::uses('VendorAssessmentFinding', 'VendorAssessments.Model');

class VendorAssessmentFinding_Deadline_Base extends NotificationsBase {
	public $internal = 'vendor_assessment_finding_deadline';
	public $model = 'VendorAssessmentFinding';
	public $reminderDays = null;
	public $customEmailTemplate = true;

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
			$this->title = __('VA Finding Deadline Approaching Soon (-%s)', $daysLabel);
			$this->description = __('Notifies %s before VA Finding expires', $daysLabel);

			$this->conditions = [
				$this->model . '.deadline' => date('Y-m-d', strtotime('+' . $absReminder . ' days')),
				$this->model . '.status' => VendorAssessmentFinding::STATUS_OPEN
			];
		}
		else {
			$this->title = __('VA Finding Deadline (%s)', $daysLabel);
			$this->description = __('Notifies %s after VA Finding expiration', $daysLabel);

			$this->conditions = [
				$this->model . '.deadline' => date('Y-m-d', strtotime('-' . $absReminder . ' days')),
				$this->model . '.status' => VendorAssessmentFinding::STATUS_OPEN
			];
		}
	}
}
