<?php
class RiskException_Expiration_004 extends RiskException_Expiration_Base {
	protected $reminderDays = 5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
