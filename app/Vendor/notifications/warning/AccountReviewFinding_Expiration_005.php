<?php
class AccountReviewFinding_Expiration_005 extends AccountReviewFinding_Expiration_Base {
	public $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
