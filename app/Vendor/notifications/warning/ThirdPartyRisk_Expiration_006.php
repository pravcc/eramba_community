<?php
class ThirdPartyRisk_Expiration_006 extends ThirdPartyRisk_Expiration_Base {
	protected $reminderDays = 20;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
