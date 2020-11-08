<?php
class SecurityServiceAudit_Begin_006 extends SecurityServiceAudit_Begin_Base {
	protected $reminderDays = 10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
