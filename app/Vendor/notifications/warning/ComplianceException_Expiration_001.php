<?php
class ComplianceException_Expiration_001 extends ComplianceException_Expiration_Base {
	protected $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
