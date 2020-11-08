<?php
class SecurityServiceMaintenance_Start_001 extends SecurityServiceMaintenance_Start_Base {
	protected $reminderDays = -1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
