<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\PhysicalSecurityPolicies;

class FireMotionDetectors extends BasePackage {
	public $alias = 'FireMotionDetectors';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Fire; Motion Detectors');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Prevent fire and unauthorized access in branch offices.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new PhysicalSecurityPolicies()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("If sensors are working"),
			'audit_success_criteria' => __("All tested sensors work as expected"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 50000,
			'capex' => 8000,
			'resource_utilization' => 4
		);

	}
}
