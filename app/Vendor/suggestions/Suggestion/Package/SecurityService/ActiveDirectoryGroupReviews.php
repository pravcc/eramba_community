<?php
namespace Suggestion\Package\SecurityService;

class ActiveDirectoryGroupReviews extends BasePackage {
	public $alias = 'ActiveDirectoryGroupReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Active Directory Group Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure that the members at our main Active Directory groups are correct'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("The groups that need to be audited are the ones listed on this policy: security; it_admin; administrator"),
			'audit_success_criteria' => __("No creeping accounts"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 20000,
			'capex' => 14000,
			'resource_utilization' => 4
		);

	}
}
