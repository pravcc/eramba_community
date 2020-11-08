<?php
class ComplianceAudit_Deadline_003 extends ComplianceAudit_Deadline_Base {
	protected $reminderDays = -1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
