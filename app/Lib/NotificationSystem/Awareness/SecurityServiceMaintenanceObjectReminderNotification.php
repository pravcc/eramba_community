<?php
App::uses('ObjectReminderNotification', 'Lib/NotificationSystem/Awareness');

class SecurityServiceMaintenanceObjectReminderNotification extends ObjectReminderNotification
{
	public function initialize()
	{
		parent::initialize();
	}

	public function handle($id)
	{
		$conds = [
			'SecurityServiceMaintenance.id' => $id,
			'SecurityServiceMaintenance.result IS NULL'
		];

		$SecurityServiceMaintenance = ClassRegistry::init('SecurityServiceMaintenance');
		$count = $SecurityServiceMaintenance->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}