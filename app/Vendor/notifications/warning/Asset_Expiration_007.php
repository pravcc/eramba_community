<?php
class Asset_Expiration_007 extends Asset_Expiration_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
