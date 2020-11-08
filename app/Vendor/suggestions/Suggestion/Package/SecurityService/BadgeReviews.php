<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\PhysicalSecurityPolicies;

class BadgeReviews extends BasePackage {
	public $alias = 'BadgeReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Badge Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Badges are assigned to our employees in order to allow them access to certain facilities. This control ensures that there are no lost badges activated on the system and that  key facilities are only accesible by the authorized personal.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new PhysicalSecurityPolicies()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("Badges correctly assigned and access to secured facilities controlled."),
			'audit_success_criteria' => __("- Active badges must match to an employee \n- No visitors badges active \n- Secured rooms x; y and z allow access only to the authorized personel."),
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
