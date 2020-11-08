<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class SecurityServiceAuditBeginNotification extends WarningNotification
{

	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Security Service Audit Deadline');

		$this->emailSubject = __(
			'Sheduled Audit for "%s" on %s',
			$this->Model->getMacroByName('security_service'),
			$this->Model->getMacroByName('planned_date')
		);

		$this->emailBody = __('Hello,

On %s, there is a scheduled audit for the control "%s".

The audit process is set as follow:
%s

And the expected results are:
%s

Follow the link below to review the control, using the menu click on "Audits" and you will find all audit records for this control including the ones missing.

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('planned_date'),
			$this->Model->getMacroByName('security_service'),
			$this->Model->getMacroByName('audit_metric_description'),
			$this->Model->getMacroByName('audit_success_criteria')
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			'SecurityServiceAudit.id' => $id,
			'SecurityServiceAudit.result IS NULL'
		];

		if ($days < 0) {
			$conds['SecurityServiceAudit.planned_date'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds['SecurityServiceAudit.planned_date'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$SecurityServiceAudit = ClassRegistry::init('SecurityServiceAudit');
		$count = $SecurityServiceAudit->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}