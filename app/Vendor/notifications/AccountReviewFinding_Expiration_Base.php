<?php
App::uses('AccountReviewFinding', 'AccountReviews.Model');

class AccountReviewFinding_Expiration_Base extends NotificationsBase {
	public $internal = 'account_review_finding_expiration';
	public $model = 'AccountReviewFinding';
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
			$this->title = __('Account Review finding deadline approaching in %s', $daysLabel);
			$this->description = __('Notifies %s before a Account Review Finding expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.deadline' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Account Review finding deadline expired by %s', $daysLabel);
			$this->description = __('Notifies %s after a Account Review Finding expires', $daysLabel);

			$this->conditions = array(
				$this->model . '.deadline' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}

		// append a general status condition to apply
		$this->conditions[$this->model . '.status !='] = AccountReviewFinding::STATUS_CLOSED;
	}
}
