<?php
class VendorAssessment_EndDate_Base extends NotificationsBase {
	public $internal = 'vendor_assessment_end_date';
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
			$this->title = __('VA Deadline Approaching Soon (-%s)', $daysLabel);
			$this->description = __('Notifies %s before scheduled Vendor Assessment ends', $daysLabel);

			$this->conditions = [
				$this->model . '.end_date' => date('Y-m-d', strtotime('+' . $absReminder . ' days')),
			];
		}
		else {
			$this->title = __('VA Deadline Approaching Soon (-%s)', $daysLabel);
			$this->description = __('Notifies %s after scheduled Vendor Assessment ends', $daysLabel);

			$this->conditions = [
				$this->model . '.end_date' => date('Y-m-d', strtotime('-' . $absReminder . ' days')),
			];
		}
	}
}
