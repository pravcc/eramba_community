<?php
App::uses('ObjectReminderNotification', 'Lib/NotificationSystem/Awareness');
App::uses('ComplianceAnalysisFinding', 'Model');

class ComplianceAnalysisFindingObjectReminderNotification extends ObjectReminderNotification
{
	public function initialize()
	{
		parent::initialize();
	}

	public function handle($id)
	{
		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.status !=' => ComplianceAnalysisFinding::STATUS_CLOSED
		];

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}