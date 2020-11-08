<?php
App::uses('ReportBaseNotification', 'NotificationSystem.Lib/NotificationSystem');

class AdvancedFiltersNotification extends ReportBaseNotification
{
	public function initialize()
	{
		$this->_label = __('Send Scheduled Filters');

		$this->emailSubject = __('Scheduled Filter');
		$this->emailBody = __('
			Hello,

You account has been defined as one recipient for this scheduled filter report, please see the attachment for details.

Regards
		');
	}

	public function handle($id)
	{
		return true;
	}
}