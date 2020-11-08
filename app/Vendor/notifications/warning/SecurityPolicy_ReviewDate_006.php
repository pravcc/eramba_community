<?php
class SecurityPolicy_ReviewDate_006 extends SecurityPolicy_ReviewDate_Base {
	protected $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
