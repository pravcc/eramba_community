<?php
class SecurityServiceMaintenance_Start_003 extends SecurityServiceMaintenance_Start_Base {
	protected $reminderDays = 5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
