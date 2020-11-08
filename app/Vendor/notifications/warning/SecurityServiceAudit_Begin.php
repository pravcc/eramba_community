<?php
App::import('Vendor', 'SecurityServiceAudit_Begin_001', array('file' => 'notifications/warning/SecurityServiceAudit_Begin_001.php'));
class SecurityServiceAudit_Begin extends SecurityServiceAudit_Begin_001 {

	public function __construct($options = array()) {
		parent::__construct($options);

		$days = $this->reminderDays;
		$daysLabel = sprintf(__n('%s day', '%s days', $days), $days);

		$this->deprecated = __('This notification is a fallback for an older configuration (%s). You will be properly notified before it\'s removal in the future.', $daysLabel);


	}
}
