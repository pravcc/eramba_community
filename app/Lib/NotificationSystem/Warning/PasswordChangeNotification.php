<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('Router', 'Routing');

class PasswordChangeNotification extends WarningNotification
{
	public $instantEmail = true;
	
	public function initialize()
	{
		$this->_label = __('Password Change');

		$this->emailSubject = __('Your password has been reseted');
		$this->emailBody = __(
			"Hello,

Someone at eramba has reseted the password for your account %%USERNAME%%, your temporal password is %%PASSWORD%%. Login at eramba using the link below and change the password before you start using the system.

%s

Regards",
			self::loginUrl()
		);
	}

	public static function loginUrl()
	{
		$link = '<a href="' . Router::url(['controller' => 'users', 'action' => 'login', 'plugin' => null], true) . '">' . __('Login URL') . '</a>';

		return $link;
	}

	public function getMacros()
	{
		return parent::getMacros() + [
			'USERNAME' => __('Username'),
			'PASSWORD' => __('Password')
		];
	}
}