<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('Router', 'Routing');

class LdapSyncFailedNotification extends WarningNotification
{
	public function initialize()
	{
		$this->_label = __('LDAP Sync Failed');

		$this->emailSubject = __('LDAP Sync Failed');
		$this->emailBody = __(
			"Hello,

We are writing to let you know that the automatic LDAP synchronisation process configured in eramba has failed because the group to synchronise does no longer exist in the LDAP Directory. Please go to system / settings / user management and review the audit trails for this sync process to review when this happened.

Regards"
		);
	}
}