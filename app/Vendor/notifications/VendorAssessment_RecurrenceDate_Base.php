<?php
App::uses('VendorAssessment', 'VendorAssessments.Model');

class VendorAssessment_RecurrenceDate_Base extends NotificationsBase {
	public $internal = 'vendor_assessment_recurrence_date';
	public $model = 'VendorAssessment';
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
			$this->title = __('Schedulled VA with a set frequency (-%s)', $daysLabel);
			$this->description = __('Triggers %s before creating and starting a schedulled VA which has a set frequency', $daysLabel);

			$date = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$this->title = __('Schedulled VA with a set frequency (-%s)', $daysLabel);
			$this->description = __('Triggers %s before creating and starting a schedulled VA which has a set frequency', $daysLabel);

			$date = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$this->conditions = VendorAssessment::getRegularFinderConditions($date);
	}
}
