<?php
class VendorAssessment_EndDate_002 extends VendorAssessment_EndDate_Base {
	public $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
