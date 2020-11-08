<?php
class ComplianceFinding_Deadline_009 extends ComplianceFinding_Deadline_Base {
	protected $reminderDays = -20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
