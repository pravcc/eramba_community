<?php
App::uses('AwarenessNotification', 'NotificationSystem.Lib/NotificationSystem');

class ObjectReminderNotification extends AwarenessNotification
{
	public function initialize()
	{
		$this->_label = __('Recurrent Awareness Reminder');

		$this->emailSubject = __('Reminder for item: %s', $this->_displayFieldMacro());
		$this->emailBody = __(
			"Hello,

A scheduled reminder has triggered for the item %s and your account is on the recipient list. Use the link below and login in eramba using your credentials to view the item.

%%ITEM_URL%%

Regards",
			$this->_displayFieldMacro()
		);
	}
}