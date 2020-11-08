<?php
namespace Suggestion\Package\SecurityService;

class ServiceAccountsReviews extends BasePackage {
	public $alias = 'ServiceAccountsReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Service Accounts Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __("Review all service accounts defined in the AD. A service account is one used by applications where the password expiration configuration is set to never expire."),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __('Get a list of AD accounts where the "password expiration field" is set to "never". Review that each account has a ticket; expiration and they are still valid.'),
			'audit_success_criteria' => __("No service accounts without a valid ticket and expiration"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => $this->getDefaultOpex(),
			'capex' => $this->getDefaultCapex(),
			'resource_utilization' => 6
		);

	}
}
