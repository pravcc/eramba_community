<?php
class ServiceContract_Expiration_002 extends ServiceContract_Expiration_Base {
	protected $reminderDays = -1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
