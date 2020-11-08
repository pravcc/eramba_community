<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class SecurityServiceMaintenanceBeginNotification extends WarningNotification
{
	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Security Service Maintenance Deadline');

		$this->emailSubject = __(
			'Scheduled Maintenance for "%s" on %s',
			$this->Model->getMacroByName('security_service'),
			$this->Model->getMacroByName('planned_date')
		);

		$this->emailBody = __('Hello,

On %s, there is a scheduled Maintenance for the control "%s".

The Maintenance process is set as follow:
%s

Follow the link below to review the control, using the menu click on "Maintenances" and you will find all maintenance records for this control including the ones missing.

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('planned_date'),
			$this->Model->getMacroByName('security_service'),
			$this->Model->getMacroByName('task')
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			'SecurityServiceMaintenance.id' => $id,
			'SecurityServiceMaintenance.result IS NULL'
		];

		if ($days < 0) {
			$conds['SecurityServiceMaintenance.planned_date'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds['SecurityServiceMaintenance.planned_date'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$SecurityServiceMaintenance = ClassRegistry::init('SecurityServiceMaintenance');
		$count = $SecurityServiceMaintenance->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}