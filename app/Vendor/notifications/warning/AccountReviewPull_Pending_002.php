<?php
class AccountReviewPull_Pending_002 extends AccountReviewPull_Pending_Base {
	public $reminderDays = 10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
