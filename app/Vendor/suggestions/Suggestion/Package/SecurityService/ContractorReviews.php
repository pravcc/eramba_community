<?php
namespace Suggestion\Package\SecurityService;

class ContractorReviews extends BasePackage {
	public $alias = 'ContractorReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Contractor Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __("Verify that all contractors in are still working for our organization and NDA's are signed."),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("Review the contractor database with HR and review that for each one of them an approval exist; their status is active and a NDA has been signed."),
			'audit_success_criteria' => __("100% of contractors in db are still working and all the relevant documentation exist"),
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
