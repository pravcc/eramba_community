<?php
class ComplianceAudit_Deadline_004 extends ComplianceAudit_Deadline_Base {
	protected $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
