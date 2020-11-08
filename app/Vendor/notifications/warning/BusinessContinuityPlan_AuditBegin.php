<?php
class BusinessContinuityPlan_AuditBegin extends BusinessContinuityPlan_AuditBegin_Base {
	protected $reminderDays = -10;
	// public $internal = 'business_continuity_plan_audit_begin';
	// public $model = 'BusinessContinuityPlan';

	public function __construct($options = array()) {
		parent::__construct($options);

		/*$this->title = __('Business Continuity Plan Audit About to Come');
		$this->description = __('Notifies 10 days before a scheduled Business Continuity Plan Audit begins');

		$this->contain = array(
			'BusinessContinuityPlanAudit' => array(
				'conditions' => array(
					'BusinessContinuityPlanAudit.planned_date' => date('Y-m-d', strtotime('+10 days'))
				),
				'fields' => array('id', 'result', 'created')
			)
		);*/
	}

	/*public function parseData($item) {
		if (!empty($item['BusinessContinuityPlanAudit'])) {
			return true;
		}

		return false;
	}*/
}
