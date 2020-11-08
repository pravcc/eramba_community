<?php
class SecurityServiceMaintenance_Start_000 extends SecurityServiceMaintenance_Start_Base {
	protected $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
