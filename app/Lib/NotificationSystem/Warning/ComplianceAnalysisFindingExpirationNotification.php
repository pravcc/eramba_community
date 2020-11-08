<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('ComplianceAnalysisFinding', 'Model');

class ComplianceAnalysisFindingExpirationNotification extends WarningNotification
{
	public function initialize()
	{
		$this->_label = __('Compliance Analysis Finding Deadline');

		$this->emailSubject = __(
			'Compliance Finding for item "%s" expiring on %s',
			$this->_displayFieldMacro(),
			$this->Model->getMacroByName('due_date')
		);

		$this->emailBody = __('Hello,

A compliance finding under the title of "%s" expires on %s and requires your attention.

- Follow the link below and login in eramba with your credentials, you will be redirected to the finding.
- You can then click on the item and edit the finding
- You can either move the expiring date in the future or change the status to "Closed"

%%ITEM_URL%%

Regards',
			$this->_displayFieldMacro(),
			$this->Model->getMacroByName('due_date')
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.status !=' => ComplianceAnalysisFinding::STATUS_CLOSED
		];

		if ($days < 0) {
			$conds[$this->Model->alias . '.due_date'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds[$this->Model->alias . '.due_date'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}