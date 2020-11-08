<?php
App::uses('Improvement', 'Model');
class GoalAuditImprovement extends Improvement {
	protected $auditModel = 'GoalAudit';
	protected $auditParentModel = 'Goal';

	public $actsAs = array(
		'ObjectStatus.ObjectStatus',
	);

	public $belongsTo = array(
		'GoalAudit',
		'User'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = [
			'goal_audit_id' => [
				'label' => __('Goal Audit'),
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

	/**
	 * Reload corrections on Business Plan.
	 */
	/*public function afterDelete() {
		return $this->SecurityServiceAudit->SecurityService->saveAudits($this->securityServiceId);
	}*/

	public function logProjects($created = false) {
	/*	$ret = true;

		$data = $this->find('first', array(
			'conditions' => array(
				'SecurityServiceAuditImprovement.id' => $this->id
			),
			'fields' => array('SecurityServiceAudit.security_service_id')
		));

		$this->SecurityServiceAudit->SecurityService->pushStatusRecords();
		$ret &= $this->SecurityServiceAudit->SecurityService->saveAudits($data['SecurityServiceAudit']['security_service_id']);
		$this->SecurityServiceAudit->SecurityService->holdStatusRecords();

		if ($created) {
			$ret &= $this->logToSecurityService($data['SecurityServiceAudit']['security_service_id']);
		}

		return $ret;*/
	}

	private function logToSecurityService($security_service_id) {
		/*
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audits = $this->SecurityServiceAudit->find('all', array(
			'conditions' => array(
				'SecurityServiceAudit.security_service_id' => $security_service_id,
				'SecurityServiceAudit.planned_date <=' => $today
			),
			'fields' => array('SecurityServiceAudit.id', 'SecurityServiceAudit.result', 'SecurityServiceAuditImprovement.id'),
			'order' => array('SecurityServiceAudit.planned_date' => 'DESC'),
			'contain' => array(
				'SecurityServiceAuditImprovement' => array(
					'Project' => array(
						'fields' => array('title')
					)
				)
			),
			// 'limit' => 1
		));

		if (isset($audits[0]) && $audits[0]['SecurityServiceAuditImprovement']['id'] == $this->id) {
			$projects = array();
			foreach ($audits[0]['SecurityServiceAuditImprovement']['Project'] as $project) {
				$projects[] = $project['title'];
			}

			$projects = implode(', ', $projects);
			$message = __('Project was assigned to the failed audit: %s', $projects);
			$this->SecurityServiceAudit->SecurityService->addNoteToLog($message);
			return $this->SecurityServiceAudit->SecurityService->setSystemRecord($security_service_id, 2);
		}*/

		return true;
	}
}
