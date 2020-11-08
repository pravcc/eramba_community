<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\PhysicalSecurityPolicies;

class CCTV extends BasePackage {
	public $alias = 'CCTV';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('CCTV');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Monitor access and specific areas in offices in order to prevent incidents or document evidence.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new PhysicalSecurityPolicies()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("Cameras working and recordings available."),
			'audit_success_criteria' => __("All videocamara running. 90 days of recording."),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 20000,
			'capex' => 14000,
			'resource_utilization' => 2
		);

	}
}
