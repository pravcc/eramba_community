<?php
class PolicyException_Expiration_008 extends PolicyException_Expiration_Base {
	protected $reminderDays = -20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
