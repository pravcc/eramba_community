<?php
class VendorAssessment_RecurrenceDate extends VendorAssessment_RecurrenceDate_Base {
	public $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
