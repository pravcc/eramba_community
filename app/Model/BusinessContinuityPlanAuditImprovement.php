<?php
App::uses('Improvement', 'Model');
class BusinessContinuityPlanAuditImprovement extends Improvement {
	protected $auditModel = 'BusinessContinuityPlanAudit';
	protected $auditParentModel = 'BusinessContinuityPlan';

	public $actsAs = array(
		'ObjectStatus.ObjectStatus',
	);
	
	public $belongsTo = array(
		'BusinessContinuityPlanAudit',
		'User'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = [
			'business_continuity_plan_audit_id' => [
				'label' => __('Business Continuity Plan Audit'),
				'editable' => false,
				'hidden' => true,
			],
			'user_id' => [
				'label' => __('User'),
				'editable' => false,
				'hidden' => true,
			],
			'Project' => [
				'label' => __('Improvement Project'),
				'editable' => true,
				'options' => [$this, 'getProjects'],
				'description' => __('The name of the project created to correct this issue.'),
			],
			'SecurityIncident' => [
				'label' => __('Security Incidents'),
				'editable' => true,
				'description' => __('Map one or more security incidents for this improvement.'),
			],
		];

		parent::__construct($id, $table, $ds);
	}

	public function getObjectStatusConfig() {
        return [
        ];
    }

	public function logProjects($created) {
		$ret = true;

		$data = $this->find('first', array(
			'conditions' => array(
				'BusinessContinuityPlanAuditImprovement.id' => $this->id
			),
			'fields' => array('BusinessContinuityPlanAudit.business_continuity_plan_id')
		));

		$this->BusinessContinuityPlanAudit->BusinessContinuityPlan->pushStatusRecords();
		$ret &= $this->BusinessContinuityPlanAudit->BusinessContinuityPlan->saveAudits($data['BusinessContinuityPlanAudit']['business_continuity_plan_id']);
		$this->BusinessContinuityPlanAudit->BusinessContinuityPlan->holdStatusRecords();

		if ($created) {
			$ret &= $this->logToBusinessPlan($data['BusinessContinuityPlanAudit']['business_continuity_plan_id']);
		}

		return $ret;
	}

	private function logToBusinessPlan($bcp_id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audits = $this->BusinessContinuityPlanAudit->find('all', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $bcp_id,
				'BusinessContinuityPlanAudit.planned_date <=' => $today
			),
			'fields' => array('BusinessContinuityPlanAudit.id', 'BusinessContinuityPlanAudit.result', 'BusinessContinuityPlanAuditImprovement.id'),
			'order' => array('BusinessContinuityPlanAudit.planned_date' => 'DESC'),
			'contain' => array(
				'BusinessContinuityPlanAuditImprovement' => array(
					'Project' => array(
						'fields' => array('title')
					)
				)
			)
		));

		if (isset($audits[0]) && $audits[0]['BusinessContinuityPlanAuditImprovement']['id'] == $this->id) {
			$projects = array();
			foreach ($audits[0]['SecurityServiceAuditImprovement']['Project'] as $project) {
				$projects[] = $project['title'];
			}

			$projects = implode(', ', $projects);
			
			$message = __('Project was assigned to the failed audit: %s', $projects);
			$this->BusinessContinuityPlanAudit->BusinessContinuityPlan->addNoteToLog($message);
			return $this->BusinessContinuityPlanAudit->BusinessContinuityPlan->setSystemRecord($bcp_id, 2);
		}

		return true;
	}
}
