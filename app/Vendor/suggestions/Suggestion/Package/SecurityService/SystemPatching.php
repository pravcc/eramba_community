<?php
namespace Suggestion\Package\SecurityService;

class SystemPatching extends BasePackage {
	public $alias = 'SystemPatching';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('System Patching');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('All the management required to keep sytems patched up to date'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("The extent to which security patches has been applied."),
			'audit_success_criteria' => __("%90 of critical and security patches applied"),
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
