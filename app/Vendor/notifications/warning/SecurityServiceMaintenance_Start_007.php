<?php
class SecurityServiceMaintenance_Start_007 extends SecurityServiceMaintenance_Start_Base {
	protected $reminderDays = -20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
