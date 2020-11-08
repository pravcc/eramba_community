<?php
App::uses('ObjectReminderNotification', 'Lib/NotificationSystem/Awareness');
App::uses('SecurityIncident', 'Model');

class ProjectAchievementObjectReminderNotification extends ObjectReminderNotification
{
	public function initialize()
	{
		parent::initialize();
	}

	public function handle($id)
	{
		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.completion <' => 100
		];

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}