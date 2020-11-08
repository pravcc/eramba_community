<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class SecurityServiceAuditPassedNotification extends WarningNotification
{

	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Security Service Audit Passed');

		$this->emailSubject = __(
			'Internal Control Audit tagged as Passed'
		);

		$this->emailBody = __('Hello,

The internal control under the name "%s" has an audit associated for the date %s - this audit has been updated and tagged as "Passed". Click on the link below to see the audit record.

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('security_service'),
			$this->Model->getMacroByName('planned_date')
		);
	}
}