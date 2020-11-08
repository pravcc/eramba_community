<?php
App::uses('DefaultNotification', 'NotificationSystem.Lib/NotificationSystem');

class CommentsNotification extends DefaultNotification
{
	public function initialize()
	{
		$this->_label = __('Comment Uploaded');

		$this->emailSubject = __('New comment for item: %s', $this->_displayFieldMacro());
		$this->emailBody = __('Hello,

A new comment has been included on for the item "%s":

<i>%%COMMENT_MESSAGE%%</i>

To respond the comment please follow the link below, login in eramba with your credentials and once you see the item click on the menu options and then "Comments & Attachments".

%%ITEM_URL%%

Regards
		', $this->_displayFieldMacro());
	}

	public function getMacros()
	{
		return parent::getMacros() + [
			'COMMENT_MESSAGE' => __('Comment Message')
		];
	}
}