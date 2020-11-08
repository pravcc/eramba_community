<?php
class ServiceContract_Expiration_006 extends ServiceContract_Expiration_Base {
	protected $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
