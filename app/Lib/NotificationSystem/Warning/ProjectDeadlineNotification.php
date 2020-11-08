<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('Project', 'Model');

class ProjectDeadlineNotification extends WarningNotification
{
	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Project Deadline');

		$this->emailSubject = __(
			'%s "%s" deadline on %s',
			$sectionLabel,
			$this->_displayFieldMacro(),
			$this->Model->getMacroByName('deadline')
		);

		$this->emailBody = __('Hello,

A Project under the title of "%s" has a deadline set for %s. Follow the link below if you want to know more about this project.

%%ITEM_URL%%

Regards',
			$this->_displayFieldMacro(),
			$this->Model->getMacroByName('deadline')
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.project_status_id' => Project::STATUS_ONGOING
		];

		if ($days < 0) {
			$conds[$this->Model->alias . '.deadline'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds[$this->Model->alias . '.deadline'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}