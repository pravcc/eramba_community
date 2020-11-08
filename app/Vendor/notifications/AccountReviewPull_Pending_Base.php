<?php
App::uses('AccountReviewPull', 'AccountReviews.Model');

class AccountReviewPull_Pending_Base extends NotificationsBase {
	public $internal = 'account_review_pull_pending';
	public $model = 'AccountReviewPull';
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
		
		$this->title = __('Pending Account Review (+%s)', $daysLabel);
		$this->description = __('Triggers %s after created pull review has not been submitted.', $daysLabel);

		$date = date('Y-m-d', strtotime('-' . $absReminder . ' days'));

		$this->conditions = array(
			'DATE(' . $this->model . '.created)' => date('Y-m-d', strtotime('-' . $absReminder . ' days')),
			$this->model . '.submitted' => AccountReviewPull::NOT_SUBMITTED,
		);
	}
}
