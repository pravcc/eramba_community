<?php
class AccountReviewPull_Pending_001 extends AccountReviewPull_Pending_Base {
	public $reminderDays = 5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
