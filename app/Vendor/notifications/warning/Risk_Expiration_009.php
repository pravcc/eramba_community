<?php
class Risk_Expiration_009 extends Risk_Expiration_Base {
	protected $reminderDays = -20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
