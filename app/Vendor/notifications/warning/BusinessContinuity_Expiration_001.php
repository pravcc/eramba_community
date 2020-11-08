<?php
class BusinessContinuity_Expiration_001 extends BusinessContinuity_Expiration_Base {
	protected $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
