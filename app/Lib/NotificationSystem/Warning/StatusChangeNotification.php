<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class StatusChangeNotification extends WarningNotification
{	
	public function initialize()
	{
		$this->emailSubject = __(
			'%s status modification for item "%s"',
			$this->Model->label([
				'singular' => true
			]),
			$this->_displayFieldMacro()
		);

		$this->emailBody = __(
			'Hello,

A %s under the title of "%s" has changed its status to %%NEW_VALUE%%. Follow the link below if you want to know more about this item.

%ITEM_URL%

Regards
',
			$this->Model->label([
				'singular' => true
			]),
			$this->_displayFieldMacro()
		);
	}

	public function getMacros()
	{
		return parent::getMacros() + [
			'OLD_VALUE' => __('Old Value'),
			'NEW_VALUE' => __('New Value'),
		];
	}
}