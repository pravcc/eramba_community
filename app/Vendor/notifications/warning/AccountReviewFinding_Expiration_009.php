<?php
class AccountReviewFinding_Expiration_009 extends AccountReviewFinding_Expiration_Base {
	public $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
