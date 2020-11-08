<?php
namespace Suggestion\Package\SecurityService;

class SecureApplicationDevelopment extends BasePackage {
	public $alias = 'SecureApplicationDevelopment';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Secure Application Development');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure security is embedded on the SDLC.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("Projects complete our SDLC checklist"),
			'audit_success_criteria' => __("Ensure that all projects since the last audit have a completed SDLC checklist and approvals."),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => $this->getDefaultOpex(),
			'capex' => $this->getDefaultCapex(),
			'resource_utilization' => 2
		);

	}
}
