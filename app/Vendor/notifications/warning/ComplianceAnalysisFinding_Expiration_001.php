<?php
class ComplianceAnalysisFinding_Expiration_001 extends ComplianceAnalysisFinding_Expiration_Base {
	protected $reminderDays = -5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
