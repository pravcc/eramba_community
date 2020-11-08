<?php
namespace Suggestion\Package\Goal;
// use Suggestion\Package\SecurityService\CCTV;

class InnovationPackage extends BasePackage {
	public $alias = 'InnovationPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Reduce Audit Costs');
		// $this->description = __('Description');

		$this->data = array(
			'name' => $this->name,
			'owner_id' => ADMIN_ID,
			'description' => __('Our goal is to reduce the cost asociated with internal audits to the point we are able to keep the quality of our audits but using one full resource less from the team to execute them.'),
			'audit_metric' => __('Effort required in terms of FTE (full time employee) '),
			'audit_criteria' => __('We should see 25% reduction on each audit.'),
			'status' => GOAL_DRAFT,

			// example of a security service suggestion
			'security_service_id' => '',
			/*'security_service_id' => array(
				new CCTV()
			),*/
			'risk_id' => '',
			'third_party_risk_id' => '',
			'business_continuity_id' => '',
			'project_id' => '',
			'security_policy_id' => '',
			'program_issue_id' => '',

			'audit_calendar' => array(
				1 => $this->getAuditDate()
			)
		);
	}
}
