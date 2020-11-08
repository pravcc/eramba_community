<?php
class PolicyException_Expiration_003 extends PolicyException_Expiration_Base {
	protected $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
