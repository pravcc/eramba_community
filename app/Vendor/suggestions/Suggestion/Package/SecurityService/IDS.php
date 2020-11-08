<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\FirewallPolicies;

class IDS extends BasePackage {
	public $alias = 'IDS';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('IDS');

		$this->data = array(
			'name' => $this->name,
			'objective' => __("OSSEC is used as a host IDS solution. The objective of this control is to ensure that the systems are working and alarms are being generated and managed correctly."),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new FirewallPolicies()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("- Ensure that systems have OSSEC installed as stated on the policy \n- Ensure that alarms have been generated since the last audit in our logging system Alarms. If there was no alarm; trigger the alarm (you will need syseng help) to ensure it's created in our logging system. \n- Ensure that all triggered alarms have tickets and incidents created that investigate what happened."),
			'audit_success_criteria' => __("%100 OSSEC related alarms being created and treated correctly since the last audit."),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 7000,
			'capex' => 1900,
			'resource_utilization' => 3
		);

	}
}
