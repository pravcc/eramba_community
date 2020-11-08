<?php
class PolicyException_Expiration_001 extends PolicyException_Expiration_Base {
	protected $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
