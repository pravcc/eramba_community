<?php
class AccountReviewPull_Pending extends AccountReviewPull_Pending_Base {
	public $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
