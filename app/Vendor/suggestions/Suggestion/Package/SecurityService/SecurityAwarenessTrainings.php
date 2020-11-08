<?php
namespace Suggestion\Package\SecurityService;

class SecurityAwarenessTrainings extends BasePackage {
	public $alias = 'SecurityAwarenessTrainings';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Security Awareness Trainings');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure all employees understand the basic concepts of our Security policies and procedures.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("eramba counts how many employees are not compliant with the awareness training."),
			'audit_success_criteria' => __("the number of employees not compliant should not exceed %10 (meaning %90 of the company is compliant with the security awareness)."),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => $this->getDefaultOpex(),
			'capex' => $this->getDefaultCapex(),
			'resource_utilization' => 1
		);

	}
}
