<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class BusinessContinuityPlanAuditBeginNotification extends WarningNotification
{

	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Business Continuity Plan Audit Deadline');

		$this->emailSubject = __(
			'Sheduled Audit for "%s" on %s',
			$this->Model->getMacroByName('business_continuity_plan'),
			$this->Model->getMacroByName('planned_date')
		);

		$this->emailBody = __('Hello,

On %s, there is a scheduled audit for business continuity plan "%s".

The audit process is set as follow:
%s

And the expected results are:
%s

Follow the link below to review audit.

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('planned_date'),
			$this->Model->getMacroByName('business_continuity_plan'),
			$this->Model->getMacroByName('metric_description'),
			$this->Model->getMacroByName('success_criteria')
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			'BusinessContinuityPlanAudit.id' => $id,
			'BusinessContinuityPlanAudit.result IS NULL'
		];

		if ($days < 0) {
			$conds['BusinessContinuityPlanAudit.planned_date'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds['BusinessContinuityPlanAudit.planned_date'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$BusinessContinuityPlanAudit = ClassRegistry::init('BusinessContinuityPlanAudit');
		$count = $BusinessContinuityPlanAudit->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}