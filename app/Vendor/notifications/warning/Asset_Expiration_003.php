<?php
class Asset_Expiration_003 extends Asset_Expiration_Base {
	protected $reminderDays = 1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
