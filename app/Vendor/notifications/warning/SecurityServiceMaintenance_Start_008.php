<?php
class SecurityServiceMaintenance_Start_008 extends SecurityServiceMaintenance_Start_Base {
	protected $reminderDays = -30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
