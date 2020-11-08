<?php
class VendorAssessmentFinding_Deadline_004 extends VendorAssessmentFinding_Deadline_Base {
	public $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
