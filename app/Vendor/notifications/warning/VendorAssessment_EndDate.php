<?php
class VendorAssessment_EndDate extends VendorAssessment_EndDate_Base {
	public $reminderDays = -15;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
