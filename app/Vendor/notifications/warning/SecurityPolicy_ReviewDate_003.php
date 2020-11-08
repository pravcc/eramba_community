<?php
class SecurityPolicy_ReviewDate_003 extends SecurityPolicy_ReviewDate_Base {
	protected $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
