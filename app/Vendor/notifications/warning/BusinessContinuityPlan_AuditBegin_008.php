<?php
class BusinessContinuityPlan_AuditBegin_008 extends BusinessContinuityPlan_AuditBegin_Base {
	protected $reminderDays = -20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
