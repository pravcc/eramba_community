<?php
class PolicyException_Expiration_009 extends PolicyException_Expiration_Base {
	protected $reminderDays = -30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
