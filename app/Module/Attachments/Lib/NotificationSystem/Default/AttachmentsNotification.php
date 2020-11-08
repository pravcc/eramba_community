<?php
App::uses('DefaultNotification', 'NotificationSystem.Lib/NotificationSystem');

class AttachmentsNotification extends DefaultNotification
{
	public function initialize()
	{
		$this->_label = __('Attachment Uploaded');

		$this->emailSubject = __('New attachment for item: %s', $this->_displayFieldMacro());
		$this->emailBody = __('Hello,

A new attachment has been included on for the item "%s":

<i>%%FILENAME%%</i>

To respond the attachment please follow the link below, login in eramba with your credentials and once you see the item click on the menu options and then "Comments & Attachments".

%%ITEM_URL%%

Regards
		', $this->_displayFieldMacro());
	}

	public function getMacros()
	{
		return parent::getMacros() + [
			'FILENAME' => __('Filename')
		];
	}
}