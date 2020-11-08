<?php
namespace Suggestion\Package\SecurityService;

class CentralisedLogging extends BasePackage {
	public $alias = 'CentralisedLogging';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Centralised Logging');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Collect and process highly relevant logs. Process them in real time when possible and alarm when certain conditions occur.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("Known risks for which we have alarms deployed must be have been triggered and incidents created for each one of them and properly treated."),
			'audit_success_criteria' => __("All alarms must be tested to ensure they are working correctly. For each triggered alarm since the last audit an incident must exist."),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 0,
			'capex' => 8000,
			'resource_utilization' => 2
		);

	}
}
