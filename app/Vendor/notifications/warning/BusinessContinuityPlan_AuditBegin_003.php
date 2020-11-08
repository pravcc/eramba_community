<?php
class BusinessContinuityPlan_AuditBegin_003 extends BusinessContinuityPlan_AuditBegin_Base {
	protected $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
