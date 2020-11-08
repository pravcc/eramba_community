<?php
class PolicyException_Expiration_004 extends PolicyException_Expiration_Base {
	protected $reminderDays = 5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
