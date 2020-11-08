<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\EndPointStandards;

class EndPointHardwareInventory extends BasePackage {
	public $alias = 'EndPointHardwareInventory';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('End-Point Hardware Inventory');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Control that hardware (laptops and computers) are built according to our company standards.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				//new EndPointStandards()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("10 randomly chosen computers from the last employees that joined the company audited and validated against our standards."),
			'audit_success_criteria' => __('%100 successful'),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 50000,
			'capex' => 8000,
			'resource_utilization' => 7,
			'service_contract_id' => '',
			'project_id' => ''
		);

	}
}
