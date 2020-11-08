<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\FirewallPolicies;

class DMZReviews extends BasePackage {
	public $alias = 'DMZReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('DMZ Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure that every firewall rule in the DMZ has followed change management procedures correctly.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new FirewallPolicies()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("Secured systems are contained within a DMZ firewall; access to this firewalls and changes on the rule base are automatically logged at our logging facility. Auditors are required to log into the logging system; review all access and firewall rules changes and validate with the network owners that an appropriate change request exist."),
			'audit_success_criteria' => __("All changes on the firewall recorded on the logging facility must have a change ticket associated"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => $this->getDefaultOpex(),
			'capex' => $this->getDefaultCapex(),
			'resource_utilization' => 3
		);

	}
}
