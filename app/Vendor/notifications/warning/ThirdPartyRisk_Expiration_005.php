<?php
class ThirdPartyRisk_Expiration_005 extends ThirdPartyRisk_Expiration_Base {
	protected $reminderDays = 10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
