<?php
namespace Suggestion\Package\SecurityService;

class NDAReviews extends BasePackage {
	public $alias = 'NDAReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('NDA Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Verify that employees and contractors have signed the NDA.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("Audit random employees according to the following criteria: Office: +500 employees: 20 nda's 250 - 499 employees: 15 nda's 0 - 249 employees: 10 nda's Special projects : 10 nda's Select 6 (six) random offices that can't be repeated until all offices have been audited."),

			'audit_success_criteria' => __("All employees NDA's must be signed"),
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
