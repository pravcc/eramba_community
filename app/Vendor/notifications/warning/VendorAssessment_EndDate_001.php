<?php
class VendorAssessment_EndDate_001 extends VendorAssessment_EndDate_Base {
	public $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
