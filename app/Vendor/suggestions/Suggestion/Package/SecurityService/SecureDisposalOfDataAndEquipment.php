<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\EndPointStandards;

class SecureDisposalOfDataAndEquipment extends BasePackage {
	public $alias = 'SecureDisposalOfDataAndEquipment';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Secure Disposal of Data and Equipment');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure all data located at systems which are re-assigned or discarded is securely discarded.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new EndPointStandards()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("The number of discarded equipment since the last audit and the number of equipment sent to be securely destroyed."),
			'audit_success_criteria' => __('The number of devices registered by Service Desk as "discarded" equipment equal to devices destroyed by our third party secure disposal company.'),
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
