<?php
class BusinessContinuityPlan_AuditBegin_005 extends BusinessContinuityPlan_AuditBegin_Base {
	protected $reminderDays = 10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
