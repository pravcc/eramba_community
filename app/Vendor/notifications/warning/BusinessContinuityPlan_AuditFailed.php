<?php
class BusinessContinuityPlan_AuditFailed extends NotificationsBase {
	public $filename = 'BusinessContinuityPlan_AuditFailed.php';
	public $internal = 'business_continuity_plan_audit_failed';
	public $model = 'BusinessContinuityPlan';
	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'BusinessContinuityPlan',
		'callback' => 'afterSave'
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Business Continuity Plan Audit Failed');
		$this->description = __('Notifies when the resut of a Business Continuity Plan Audit is failed');

		$this->contain = array(
			'BusinessContinuityPlanAudit' => array(
				'conditions' => array(
					'result' => AUDIT_FAILED
				),
				'fields' => array('id', 'result', 'created')
			)
		);
	}

	public function parseData($item) {
		if (!empty($item['BusinessContinuityPlanAudit'])) {
			return true;
		}

		return false;
	}
}
