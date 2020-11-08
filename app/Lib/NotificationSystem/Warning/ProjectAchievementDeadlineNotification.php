<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class ProjectAchievementDeadlineNotification extends WarningNotification
{

	public function initialize()
	{
		$this->_label = __('Project Task Deadline');

		$this->emailSubject = __(
			'Task deadline on "%s"',
			$this->Model->getMacroByName('deadline')
		);

		$this->emailBody = __('Hello,

A task under the description:

%s

Has a deadline set for %s. Follow the link below if you want to know more about this finding.

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('description'),
			$this->Model->getMacroByName('deadline')
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.completion <' => 100
		];

		if ($days < 0) {
			$conds[$this->Model->alias . '.date'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds[$this->Model->alias . '.date'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$item = $this->Model->find('first', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		if (!empty($item['ProjectAchievement']['id'])) {
			return true;
		}

		return false;
	}
}