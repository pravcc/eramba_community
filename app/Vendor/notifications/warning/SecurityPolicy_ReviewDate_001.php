<?php
class SecurityPolicy_ReviewDate_001 extends SecurityPolicy_ReviewDate_Base {
	protected $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
