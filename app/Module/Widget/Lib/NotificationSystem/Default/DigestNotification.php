<?php
App::uses('DefaultNotification', 'NotificationSystem.Lib/NotificationSystem');

class DigestNotification extends DefaultNotification
{
	public function initialize()
	{
		$this->emailSubject = __('Digest of Comments and Attachments for item: %s', $this->_displayFieldMacro());
		$this->emailBody = __('Hello,

			Digest of Comments and Attachments for item "%s":

			New Comments:

			%%LIST_OF_NEW_COMMENTS%%

			New Attachments:

			%%LIST_OF_NEW_ATTACHMENTS%%

			Use the link below and click "Comments and Attachments" in the item menu to check what is new.

			%%ITEM_URL%%

			Regards
		', $this->_displayFieldMacro());
	}

	public function getMacros()
	{
		return parent::getMacros() + [
			'LIST_OF_NEW_COMMENTS' => __('List of new Comments'),
			'LIST_OF_NEW_ATTACHMENTS' => __('List of new Attachments')
		];
	}
}