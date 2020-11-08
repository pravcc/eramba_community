<?php
namespace Suggestion\Package\SecurityService;

class PublicSharedDrivesScanner extends BasePackage {
	public $alias = 'PublicSharedDrivesScanner';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Public Shared Drives Scanner');

		$this->data = array(
			'name' => $this->name,
			'objective' => __("Detect Public Passwordless network shares in Users Computers."),
			'security_service_type_id' => SECURITY_SERVICE_DESIGN,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("Check One location for Shares. Choose 10 to 20 Shares and check they are not accessible."),
			'audit_success_criteria' => __("No Share found can be public with no password"),
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
