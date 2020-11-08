<?php
class ComplianceAudit_Deadline_005 extends ComplianceAudit_Deadline_Base {
	protected $reminderDays = 5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
