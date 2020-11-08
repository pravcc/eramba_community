<?php
class SecurityServiceAudit_Begin_009 extends SecurityServiceAudit_Begin_Base {
	protected $reminderDays = -20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
