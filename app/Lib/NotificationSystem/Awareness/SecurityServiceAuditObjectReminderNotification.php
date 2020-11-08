<?php
App::uses('ObjectReminderNotification', 'Lib/NotificationSystem/Awareness');

class SecurityServiceAuditObjectReminderNotification extends ObjectReminderNotification
{
	public function initialize()
	{
		parent::initialize();
	}

	public function handle($id)
	{
		$conds = [
			'SecurityServiceAudit.id' => $id,
			'SecurityServiceAudit.result IS NULL'
		];

		$SecurityServiceAudit = ClassRegistry::init('SecurityServiceAudit');
		$count = $SecurityServiceAudit->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}