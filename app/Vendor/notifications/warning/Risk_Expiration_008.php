<?php
class Risk_Expiration_008 extends Risk_Expiration_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
