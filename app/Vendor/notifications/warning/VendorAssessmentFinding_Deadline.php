<?php
class VendorAssessmentFinding_Deadline extends VendorAssessmentFinding_Deadline_Base {
	public $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
