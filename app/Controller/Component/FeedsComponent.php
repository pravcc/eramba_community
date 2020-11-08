<?php
App::uses('Component', 'Controller');
class FeedsComponent extends Component {
	private $today;

	public function initialize(Controller $controller) {
		$this->controller = $controller;

		$this->today = CakeTime::format( 'Y-m-d', CakeTime::fromString( 'now' ) );
	}

	/**
	 * Get feed information.
	 */
	public function getFeed() {
		$feed_arr = array(
			'notifications' => $this->getNotifications(),
			//'workflow_requested_validation' => $this->getWorkflowRequestedValidation(),
			'used_controls' => $this->getUsedControls(),
			'controls_missing' => $this->getMissingAuditsMaintenances(),
			'contract_expired' => $this->getExpiredContracts(),
			'bcm_missing_audits' => $this->getBcmMissingAudits(),
			'security_policies' => $this->getSecurityPolicies(),
			'policy_exceptions' => $this->getPolicyExceptions(),
			'risks' => $this->getRisks(),
			'tp_risks' => $this->getThirdPartyRisks(),
			'business_risks' => $this->getBusinessRisks(),
			'risk_exceptions' => $this->getRiskExceptions(),
			'compliance_exceptions' => $this->getComplianceExceptions(),
			'compliance_findings' => $this->getComplianceFindings()
		);

		$feeds = array();
		foreach ( $feed_arr['notifications'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => $feed['title'],
				'time' => $feed['time'],
				'date' => $feed['date'],
				'url' => $feed['url']
			);
		}

		/*foreach ( $feed_arr['workflow_requested_validation'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Item "%s" needs validation.', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}*/

		foreach ( $feed_arr['used_controls'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Control "%s" is not in Production but still in use.', $feed['title'] ),
				'time' => false,
				'date' => false
			);
		}

		foreach ( $feed_arr['controls_missing']['audits'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Control "%s" has missing Audits', $feed['title'] ),
				'time' => false,
				'date' => false
			);
		}

		foreach ( $feed_arr['controls_missing']['maintenances'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Control "%s" has missing Maintenances', $feed['title'] ),
				'time' => false,
				'date' => false
			);
		}

		foreach ( $feed_arr['contract_expired'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Support Contract "%s" has expired', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['bcm_missing_audits'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Continuity Plan "%s" has missing Audits', $feed['title'] ),
				'time' => false,
				'date' => false
			);
		}

		foreach ( $feed_arr['security_policies'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Security Policy "%s" is still on Draft but used in a Security Control', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['policy_exceptions'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_CONTROLS,
				'title' => __( 'Policy Exception "%s" has expired', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['risks'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_RISK,
				'title' => __( 'Asset Risk "%s" has expired', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['tp_risks'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_RISK,
				'title' => __( 'Third Party Risk "%s" has expired', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['business_risks'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_RISK,
				'title' => __( 'Business Unit Risk "%s" has expired', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['risk_exceptions'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_RISK,
				'title' => __( 'Risk Exception "%s" has expired', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['compliance_exceptions'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_COMPLIANCE,
				'title' => __( 'Compliance Exception "%s" has expired', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		foreach ( $feed_arr['compliance_findings'] as $feed ) {
			$feeds[] = array(
				'color' => COLOR_COMPLIANCE,
				'title' => __( 'Audit Finding "%s" has expired.', $feed['title'] ),
				'time' => $feed['time'],
				'date' => $feed['date']
			);
		}

		$this->sortFeeds( $feeds );

		return $feeds;
	}

	private function sortFeeds(&$data) {
		$dates = array();
		foreach ($data as $key => $item) {
			$dates[$key] = $item['date'];
		}

		array_multisort($dates, SORT_DESC, $data);
	}

	private function getNotifications() {
		$this->controller->loadModel('Notification');
		$data = $this->controller->Notification->find('all', array(
			'conditions' => array(
				'Notification.user_id' => $this->controller->logged['id']
			),
			'recursive' => -1
		));

		$notifications = array();
		if (!empty($data)) {
			foreach ($data as $item) {
				$notifications[] = array(
					'title' => $item['Notification']['title'],
					'time' => CakeTime::timeAgoInWords($item['Notification']['created']),
					'date' => $item['Notification']['created'],
					'url' => $item['Notification']['url']
				);
			}
		}

		return $notifications;
	}

	private function getWorkflowRequestedValidation() {
		$this->controller->loadModel('SecurityIncident');
		$this->controller->loadModel('Workflow');
		$this->controller->loadModel('WorkflowLog');
		$data = $this->controller->SecurityIncident->find('all', array(
			'conditions' => array(
				'SecurityIncident.workflow_status' => WORKFLOW_GET_VALIDATION
			),
			'recursive' => -1
		));

		$request_validation = array();
		if (!empty($data)) {
			$workflowInfo = $this->controller->Workflow->getValidatorsApproversByModel('SecurityIncident');
		
			if (in_array($this->controller->logged['id'], $workflowInfo['validators'])) {
				foreach ($data as $item) {
					$log = $this->controller->WorkflowLog->find('first', array(
						'conditions' => array(
							'WorkflowLog.foreign_key' => $item['SecurityIncident']['id'],
							'WorkflowLog.model' => 'SecurityIncident',
							'WorkflowLog.status' => WORKFLOW_GET_VALIDATION
						),
						'order' => array('WorkflowLog.created' => 'DESC'),
						'recursive' => -1
					));

					$request_validation[] = array(
						'title' => $item['SecurityIncident']['title'],
						'time' => CakeTime::timeAgoInWords($log['WorkflowLog']['created']),
						'date' => $log['WorkflowLog']['created']
					);
				}
			}
		}

		return $request_validation;
	}

	/**
	 * Get Security Service list that are in use and on production status.
	 */
	private function getUsedControls() {
		$this->controller->loadModel( 'SecurityService' );
		$data = $this->controller->SecurityService->find( 'all', array(
			'conditions' => array(
				'SecurityService.security_service_type_id' => SECURITY_SERVICE_PRODUCTION
			),
			'fields' => array( 'id', 'name' ),
			'contain' => array(
				'Risk' => array(
					'fields' => array( 'id', 'title' )
				),
				'ThirdPartyRisk' => array(
					'fields' => array( 'id', 'title' )
				),
				'SecurityIncident' => array(
					'fields' => array( 'id', 'title' )
				),
				'DataAsset' => array(
					'fields' => array( 'id', 'description' )
				),
				'ComplianceManagement' => array(
					'fields' => array( 'id' )
				)
			)
		) );

		$used_controls = array();
		foreach ( $data as $item ) {
			if ( ! empty( $item['Risk'] ) || ! empty( $item['ThirdPartyRisk'] ) || ! empty( $item['SecurityIncident'] ) ||
				! empty( $item['DataAsset'] ) || ! empty( $item['ComplianceManagement'] ) ) {
				$used_controls[] = array(
					'title' => $item['SecurityService']['name']
				);
			}
		}

		return $used_controls;
	}

	/**
	 * Get Security Service items that have missing audits and maintenances.
	 */
	private function getMissingAuditsMaintenances() {
		$this->controller->loadModel( 'SecurityService' );
		$data = $this->controller->SecurityService->find( 'all', array(
			'fields' => array( 'id', 'name', 'audits_all_done', 'maintenances_all_done' ),
			'recursive' => -1
		) );

		$missing_audits = array();
		$missing_maintenances = array();
		foreach ( $data as $item ) {
			if ( ! $item['SecurityService']['audits_all_done'] ) {
				$missing_audits[] = array(
					'title' => $item['SecurityService']['name']
				);
			}

			if ( ! $item['SecurityService']['maintenances_all_done'] ) {
				$missing_maintenances[] = array(
					'title' => $item['SecurityService']['name']
				);
			}
		}

		return array(
			'audits' => $missing_audits,
			'maintenances' => $missing_maintenances
		);
	}

	/**
	 * Get expired Service Contracts.
	 */
	private function getExpiredContracts() {
		$this->controller->loadModel( 'ServiceContract' );
		$data = $this->controller->ServiceContract->find( 'all', array(
			'conditions' => array(
				'ServiceContract.end <' => $this->today
			),
			'fields' => array( 'id', 'name', 'end' ),
			'recursive' => -1
		) );

		$expired_contracts = array();
		foreach ( $data as $item ) {
			$expired_contracts[] = array(
				'title' => $item['ServiceContract']['name'],
				'time' => CakeTime::timeAgoInWords( $item['ServiceContract']['end'] ),
				'date' => $item['ServiceContract']['end']
			);
		}

		return $expired_contracts;
	}

	/**
	 * Get Business Continuity Plan missing audits.
	 */
	private function getBcmMissingAudits() {
		$this->controller->loadModel( 'BusinessContinuityPlan' );
		$data = $this->controller->BusinessContinuityPlan->find( 'all', array(
			'fields' => array( 'id', 'title', 'audits_all_done' ),
			'recursive' => -1
		) );
		
		$missing_audits = array();
		foreach ( $data as $item ) {
			if ( ! $item['BusinessContinuityPlan']['audits_all_done'] ) {
				$missing_audits[] = array(
					'title' => $item['BusinessContinuityPlan']['title']
				);
			}
		}

		return $missing_audits;
	}

	/**
	 * Get Draft Security Policies that are still in use in controls.
	 */
	private function getSecurityPolicies() {
		$this->controller->loadModel( 'SecurityPolicy' );
		$data = $this->controller->SecurityPolicy->find( 'all', array(
			'conditions' => array(
				'SecurityPolicy.status' => SECURITY_POLICY_DRAFT
			),
			'fields' => array( 'id', 'index', 'modified' ),
			'contain' => array(
				'SecurityService' => array(
					'fields' => array( 'id' )
				)
			)
		) );

		$security_policies = array();
		foreach ( $data as $item ) {
			if ( empty( $item['SecurityService'] ) ) {
				continue;
			}

			$security_policies[] = array(
				'title' => $item['SecurityPolicy']['index'],
				'time' => CakeTime::timeAgoInWords( $item['SecurityPolicy']['modified'] ),
				'date' => $item['SecurityPolicy']['modified']
			);
		}

		return $security_policies;
	}

	/**
	 * Get expired and open Policy Exception.
	 */
	private function getPolicyExceptions() {
		$this->controller->loadModel( 'PolicyException' );
		$data = $this->controller->PolicyException->find( 'all', array(
			'conditions' => array(
				'PolicyException.status' => EXCEPTION_OPEN,
				'PolicyException.expiration <' => $this->today
			),
			'fields' => array( 'id', 'title', 'expiration' ),
			'recursive' => -1
		) );

		$policy_exceptions = array();
		foreach ( $data as $item ) {
			$policy_exceptions[] = array(
				'title' => $item['PolicyException']['title'],
				'time' => CakeTime::timeAgoInWords( $item['PolicyException']['expiration'] ),
				'date' => $item['PolicyException']['expiration']
			);
		}

		return $policy_exceptions;
	}

	/**
	 * Get expired Risks.
	 */
	private function getRisks() {
		$this->controller->loadModel( 'Risk' );
		$data = $this->controller->Risk->find( 'all', array(
			'conditions' => array(
				'Risk.review <' => $this->today
			),
			'fields' => array( 'id', 'title', 'review' ),
			'recursive' => -1
		) );

		$risks = array();
		foreach ( $data as $item ) {
			$risks[] = array(
				'title' => $item['Risk']['title'],
				'time' => CakeTime::timeAgoInWords( $item['Risk']['review'] ),
				'date' => $item['Risk']['review']
			);
		}

		return $risks;
	}

	/**
	 * Get expired Third Party Risks.
	 */
	private function getThirdPartyRisks() {
		$this->controller->loadModel( 'ThirdPartyRisk' );
		$data = $this->controller->ThirdPartyRisk->find( 'all', array(
			'conditions' => array(
				'ThirdPartyRisk.review <' => $this->today
			),
			'fields' => array( 'id', 'title', 'review' ),
			'recursive' => -1
		) );

		$risks = array();
		foreach ( $data as $item ) {
			$risks[] = array(
				'title' => $item['ThirdPartyRisk']['title'],
				'time' => CakeTime::timeAgoInWords( $item['ThirdPartyRisk']['review'] ),
				'date' => $item['ThirdPartyRisk']['review']
			);
		}

		return $risks;
	}

	/**
	 * Get expired Business Risks.
	 */
	private function getBusinessRisks() {
		$this->controller->loadModel( 'BusinessContinuity' );
		$data = $this->controller->BusinessContinuity->find( 'all', array(
			'conditions' => array(
				'BusinessContinuity.review <' => $this->today
			),
			'fields' => array( 'id', 'title', 'review' ),
			'recursive' => -1
		) );

		$risks = array();
		foreach ( $data as $item ) {
			$risks[] = array(
				'title' => $item['BusinessContinuity']['title'],
				'time' => CakeTime::timeAgoInWords( $item['BusinessContinuity']['review'] ),
				'date' => $item['BusinessContinuity']['review']
			);
		}

		return $risks;
	}

	/**
	 * Get expired and open Risk Exception.
	 */
	private function getRiskExceptions() {
		$this->controller->loadModel( 'RiskException' );
		$data = $this->controller->RiskException->find( 'all', array(
			'conditions' => array(
				'RiskException.status' => EXCEPTION_OPEN,
				'RiskException.expiration <' => $this->today
			),
			'fields' => array( 'id', 'title', 'expiration' ),
			'recursive' => -1
		) );

		$risk_exceptions = array();
		foreach ( $data as $item ) {
			$risk_exceptions[] = array(
				'title' => $item['RiskException']['title'],
				'time' => CakeTime::timeAgoInWords( $item['RiskException']['expiration'] ),
				'date' => $item['RiskException']['expiration']
			);
		}

		return $risk_exceptions;
	}

	/**
	 * Get expired and open Compliance Exception.
	 */
	private function getComplianceExceptions() {
		$this->controller->loadModel( 'ComplianceException' );
		$data = $this->controller->ComplianceException->find( 'all', array(
			'conditions' => array(
				'ComplianceException.status' => EXCEPTION_OPEN,
				'ComplianceException.expiration <' => $this->today
			),
			'fields' => array( 'id', 'title', 'expiration' ),
			'recursive' => -1
		) );

		$compliance_exceptions = array();
		foreach ( $data as $item ) {
			$compliance_exceptions[] = array(
				'title' => $item['ComplianceException']['title'],
				'time' => CakeTime::timeAgoInWords( $item['ComplianceException']['expiration'] ),
				'date' => $item['ComplianceException']['expiration']
			);
		}

		return $compliance_exceptions;
	}

	/**
	 * Get expired and open Compliance Findings.
	 */
	private function getComplianceFindings() {
		$this->controller->loadModel( 'ComplianceFinding' );
		$data = $this->controller->ComplianceFinding->find( 'all', array(
			'conditions' => array(
				'ComplianceFinding.compliance_finding_status_id' => COMPLIANCE_FINDING_OPEN,
				'ComplianceFinding.deadline <' => $this->today
			),
			'fields' => array( 'id', 'title', 'deadline' ),
			'recursive' => -1
		) );

		$compliance_findings = array();
		foreach ( $data as $item ) {
			$compliance_findings[] = array(
				'title' => $item['ComplianceFinding']['title'],
				'time' => CakeTime::timeAgoInWords( $item['ComplianceFinding']['deadline'] ),
				'date' => $item['ComplianceFinding']['deadline']
			);
		}

		return $compliance_findings;
	}

}
