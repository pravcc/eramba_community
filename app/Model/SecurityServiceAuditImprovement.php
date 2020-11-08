<?php
App::uses('Improvement', 'Model');
class SecurityServiceAuditImprovement extends Improvement {
	protected $auditModel = 'SecurityServiceAudit';
	protected $auditParentModel = 'SecurityService';

	public $actsAs = array(
		'EventManager.EventManager',
		'ObjectStatus.ObjectStatus'
	);

	public $belongsTo = array(
		'SecurityServiceAudit',
		'User'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Audit Improvements');

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = [
			'security_service_audit_id' => [
				'type' => 'hidden',
				'label' => __('Security Service Audit'),
				'editable' => true,
				// 'hidden' => true,
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

	// returns joins array for use in find query that connects mostly used models
	public function getRelatedJoins() {
		return [
			[
				'table' => 'security_service_audit_improvements',
				'alias' => 'SecurityServiceAuditImprovement',
				'type' => 'INNER',
				'conditions' => [
					'ProjectsSecurityServiceAuditImprovement.security_service_audit_improvement_id = SecurityServiceAuditImprovement.id'
				]
			],
			[
				'table' => 'security_service_audits',
				'alias' => 'SecurityServiceAudit',
				'type' => 'INNER',
				'conditions' => [
					'SecurityServiceAuditImprovement.security_service_audit_id = SecurityServiceAudit.id'
				]
			],
			[
				'table' => 'projects',
				'alias' => 'Project',
				'type' => 'LEFT',
				'conditions' => [
					'ProjectsSecurityServiceAuditImprovement.project_id = Project.id'
				]
			],
		];
	}

	public function getObjectStatusConfig() {
        return [
        ];
    }

	public function logProjects($created = false) {
		$ret = true;

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

		return $ret;
	}

	private function logToSecurityService($security_service_id) {
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
		}

		return true;
	}
}
