<?php
class VendorAssessmentFinding_Deadline_003 extends VendorAssessmentFinding_Deadline_Base {
	public $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
