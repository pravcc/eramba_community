<?php
class AccountReviewPull_Pending_004 extends AccountReviewPull_Pending_Base {
	public $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
