<?php
class AccountReviewPull_Pending_003 extends AccountReviewPull_Pending_Base {
	public $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
