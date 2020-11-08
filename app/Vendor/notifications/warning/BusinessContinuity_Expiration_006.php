<?php
class BusinessContinuity_Expiration_006 extends BusinessContinuity_Expiration_Base {
	protected $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
