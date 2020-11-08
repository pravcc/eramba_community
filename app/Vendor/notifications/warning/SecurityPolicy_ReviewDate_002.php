<?php
class SecurityPolicy_ReviewDate_002 extends SecurityPolicy_ReviewDate_Base {
	protected $reminderDays = -1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
