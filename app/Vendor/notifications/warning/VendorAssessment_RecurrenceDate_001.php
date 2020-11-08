<?php
class VendorAssessment_RecurrenceDate_001 extends VendorAssessment_RecurrenceDate_Base {
	public $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
