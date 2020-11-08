<?php
class RiskException_Expiration_006 extends RiskException_Expiration_Base {
	protected $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
