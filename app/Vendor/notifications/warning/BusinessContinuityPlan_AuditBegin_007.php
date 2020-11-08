<?php
class BusinessContinuityPlan_AuditBegin_007 extends BusinessContinuityPlan_AuditBegin_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
