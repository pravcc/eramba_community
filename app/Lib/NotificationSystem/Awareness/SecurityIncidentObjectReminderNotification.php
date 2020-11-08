<?php
App::uses('ObjectReminderNotification', 'Lib/NotificationSystem/Awareness');
App::uses('SecurityIncident', 'Model');

class SecurityIncidentObjectReminderNotification extends ObjectReminderNotification
{
	public function initialize()
	{
		parent::initialize();
	}

	public function handle($id)
	{
		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.security_incident_status_id' => SecurityIncident::STATUS_ONGOING
		];

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}