<?php
App::uses('ObjectReminderNotification', 'Lib/NotificationSystem/Awareness');
App::uses('PolicyException', 'Model');

class ExceptionObjectReminderNotification extends ObjectReminderNotification
{
	public function initialize()
	{
		parent::initialize();
	}

	public function handle($id)
	{
		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.status !=' => PolicyException::STATUS_CLOSED
		];

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}