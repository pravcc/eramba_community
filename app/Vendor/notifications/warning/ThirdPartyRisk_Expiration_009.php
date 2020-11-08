<?php
class ThirdPartyRisk_Expiration_009 extends ThirdPartyRisk_Expiration_Base {
	protected $reminderDays = -30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
