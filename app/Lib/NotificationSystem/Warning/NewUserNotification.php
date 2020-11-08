<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('Router', 'Routing');

class NewUserNotification extends WarningNotification
{
	public $instantEmail = true;
	
	public function initialize()
	{
		$this->_label = __('New User');

		$this->emailSubject = __('You have been assigned an account at eramba');
		$this->emailBody = __(
			"Hello,

An account has been created at eramba asociated to this email. Your username is %%USERNAME%% and your temporal password %%PASSWORD%%, login at eramba using the link below and reset your password.

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
			'PASSWORD' => __('Password'),
			'MAIN_PORTAL_LOGIN_URL' => __('Main Portal Login URL'),
			'ONLINE_ASSESSMENT_PORTAL_LOGIN_URL' => __('Online Assessment Portal Login URL'),
			'ACCOUNT_REVIEW_PORTAL_LOGIN_URL' => __('Account Review Portal Login URL'),
 		];
	}
}