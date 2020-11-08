<?php
class VendorAssessmentFinding_Deadline_001 extends VendorAssessmentFinding_Deadline_Base {
	public $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
