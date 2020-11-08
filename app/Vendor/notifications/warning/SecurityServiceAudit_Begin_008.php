<?php
class SecurityServiceAudit_Begin_008 extends SecurityServiceAudit_Begin_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
