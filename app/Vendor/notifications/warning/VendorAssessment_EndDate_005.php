<?php
class VendorAssessment_EndDate_005 extends VendorAssessment_EndDate_Base {
	public $reminderDays = -30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
