<?php
class BusinessContinuity_Expiration_005 extends BusinessContinuity_Expiration_Base {
	protected $reminderDays = 10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
