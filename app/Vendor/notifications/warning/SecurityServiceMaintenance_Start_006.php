<?php
class SecurityServiceMaintenance_Start_006 extends SecurityServiceMaintenance_Start_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
