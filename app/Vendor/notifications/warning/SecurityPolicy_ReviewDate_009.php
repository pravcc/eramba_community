<?php
class SecurityPolicy_ReviewDate_009 extends SecurityPolicy_ReviewDate_Base {
	protected $reminderDays = -30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
