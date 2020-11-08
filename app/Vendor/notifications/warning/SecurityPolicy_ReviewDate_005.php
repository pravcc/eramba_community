<?php
class SecurityPolicy_ReviewDate_005 extends SecurityPolicy_ReviewDate_Base {
	protected $reminderDays = 10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
