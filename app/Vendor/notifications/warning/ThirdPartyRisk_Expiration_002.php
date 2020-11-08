<?php
class ThirdPartyRisk_Expiration_002 extends ThirdPartyRisk_Expiration_Base {
	protected $reminderDays = -1;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
