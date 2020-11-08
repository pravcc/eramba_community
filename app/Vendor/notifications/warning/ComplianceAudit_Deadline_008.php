<?php
class ComplianceAudit_Deadline_008 extends ComplianceAudit_Deadline_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
