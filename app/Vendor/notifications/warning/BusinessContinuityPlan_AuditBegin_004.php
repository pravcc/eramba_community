<?php
class BusinessContinuityPlan_AuditBegin_004 extends BusinessContinuityPlan_AuditBegin_Base {
	protected $reminderDays = 5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
