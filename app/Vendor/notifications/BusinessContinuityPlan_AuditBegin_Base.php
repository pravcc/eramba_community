<?php
class BusinessContinuityPlan_AuditBegin_Base extends NotificationsBase {
	public $internal = 'business_continuity_plan_audit_begin';
	public $model = 'BusinessContinuityPlan';
	protected $reminderDays = null;

	public function __construct($options = array()) {
		parent::__construct($options);

		if ($this->reminderDays === null) {
			return false;
		}

		$days = $this->reminderDays;

		// always positive number
		$absReminder = abs($days);
		$daysLabel = sprintf(__n('%s day', '%s days', $absReminder), $absReminder);
		
		if ($days < 0) {
			$this->title = __('Business Continuity Plan Audit Deadline (-%s)', $daysLabel);
			$this->description = __('Notifies %s before a scheduled Business Continuity Plan Audit begins', $daysLabel);

			$this->contain = array(
				'BusinessContinuityPlanAudit' => array(
					'conditions' => array(
						'BusinessContinuityPlanAudit.planned_date' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
					),
					'fields' => array('id', 'result', 'created')
				)
			);

			// $this->conditions = array(
			// 	$this->model . '.planned_date' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			// );
		}
		else {
			$this->title = __('Business Continuity Plan Audit Deadline (+%s)', $daysLabel);
			$this->description = __('Notifies %s after a scheduled Business Continuity Plan Audit begins', $daysLabel);

			$this->contain = array(
				'BusinessContinuityPlanAudit' => array(
					'conditions' => array(
						'BusinessContinuityPlanAudit.planned_date' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
					),
					'fields' => array('id', 'result', 'created')
				)
			);

			/*$this->conditions = array(
				$this->model . '.planned_date' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);*/
		}
	}

	public function parseData($item) {
		if (!empty($item['BusinessContinuityPlanAudit'])) {
			return true;
		}

		return false;
	}
}
