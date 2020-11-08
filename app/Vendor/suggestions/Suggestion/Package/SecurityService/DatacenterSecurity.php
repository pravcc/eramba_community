<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\PhysicalSecurityPolicies;

class DatacenterSecurity extends BasePackage {
	public $alias = 'DatacenterSecurity';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Datacenter Security');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure that data rooms; server rooms and datacenters comply with our policies and standards.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new PhysicalSecurityPolicies()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("Number of requirements for each site/dc type complied."),
			'audit_success_criteria' => __("%100 of non-compliant from last audit should be fixed (those with exceptions do not count)"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 0,
			'capex' => 5000,
			'resource_utilization' => 1
		);

	}
}
