<?php
class ComplianceFinding_Deadline_008 extends ComplianceFinding_Deadline_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
