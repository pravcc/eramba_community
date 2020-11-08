<?php
namespace Suggestion\Package\SecurityService;

class NetworkAdministratorReviews extends BasePackage {
	public $alias = 'NetworkAdministratorReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Network Administrator Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Network devices are managed by the network team. Access to this devices is authenticated by the use of an active directory group (network_team) where only the network team members (and some of their service accounts) are allowed be. This control ensures only authorized people is part of that group.'),

			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("The audit controls two things: \n1- Number of creeping privileges on the network group defined on the AD \n2- Random %10  network devices check showing Radius is configured."),

			'audit_success_criteria' => __("1- no creeping accounts\n2- all audited devies use radius to authenticate"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => $this->getDefaultOpex(),
			'capex' => $this->getDefaultCapex(),
			'resource_utilization' => 2
		);

	}
}
