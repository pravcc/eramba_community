<?php
class ComplianceException_Expiration_009 extends ComplianceException_Expiration_Base {
	protected $reminderDays = -30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
