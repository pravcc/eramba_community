<?php
namespace Suggestion\Package\SecurityService;

class SAPApplicationAccountReviews extends BasePackage {
	public $alias = 'SAPApplicationAccountReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('SAP Application - Account Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure no creeping user accounts exist on SAP'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("Get the list of SAP accounts and validate against the manager of HR and FI that the accounts are truly needed. Review all accounts disabled and validate that the date the account was removed in SAP corresponds with the date the employee left the company."),
			'audit_success_criteria' => __("No creeping accounts"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => $this->getDefaultOpex(),
			'capex' => $this->getDefaultCapex(),
			'resource_utilization' => 4
		);

	}
}
