<?php
class ComplianceException_Expiration extends ComplianceException_Expiration_Base {
	protected $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
