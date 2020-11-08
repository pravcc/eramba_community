<?php
class ComplianceFinding_Deadline_004 extends ComplianceFinding_Deadline_Base {
	protected $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
