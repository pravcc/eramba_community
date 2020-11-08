<?php
class SecurityServiceAudit_Begin_004 extends SecurityServiceAudit_Begin_Base {
	protected $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
