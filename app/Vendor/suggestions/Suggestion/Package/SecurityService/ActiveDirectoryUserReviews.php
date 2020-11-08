<?php
namespace Suggestion\Package\SecurityService;
// use Suggestion\Package\SecurityPolicy\EmployeeLifecycle;

class ActiveDirectoryUserReviews extends BasePackage {
	public $alias = 'ActiveDirectoryUserReviews';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Active Directory User Reviews');

		$this->data = array(
			'name' => $this->name,
			'objective' => __('Ensure that those employees that have left the company have no valid account in the AD and that his/her last login is previous to it\'s last day in the office.'),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => array(
				// new EmployeeLifecycle()
			),
			'documentation_url' => '',
			'audit_metric_description' => __("Get the list of employees (login and day they left the company) that left the copmany since the last audit\n- Get the list of valid AD account logins and their lastlogin For each employee that left the company; ensure he/she does not have a valid account and their last login date on the AD is previous to its last day in the company."),
			'audit_success_criteria' => __("If there is any employee with a valid account or a login posterior to it's last day; find out what happened; raise an incident and if there is something to be fixed; initiate a project."),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Default'),
			'opex' => 2000,
			'capex' => 2000,
			'resource_utilization' => 1
		);

	}
}
