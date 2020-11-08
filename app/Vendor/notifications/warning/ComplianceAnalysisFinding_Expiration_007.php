<?php
class ComplianceAnalysisFinding_Expiration_007 extends ComplianceAnalysisFinding_Expiration_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
