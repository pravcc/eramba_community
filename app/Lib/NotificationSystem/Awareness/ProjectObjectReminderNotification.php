<?php
App::uses('ObjectReminderNotification', 'Lib/NotificationSystem/Awareness');
App::uses('Project', 'Model');

class ProjectObjectReminderNotification extends ObjectReminderNotification
{
	public function initialize()
	{
		parent::initialize();
	}

	public function handle($id)
	{
		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.project_status_id' => Project::STATUS_ONGOING
		];

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}