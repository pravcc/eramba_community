<?php
class AccountReviewFinding_Expiration_008 extends AccountReviewFinding_Expiration_Base {
	public $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
