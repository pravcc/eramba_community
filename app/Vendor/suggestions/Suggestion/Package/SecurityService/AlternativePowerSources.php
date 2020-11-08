<?php
namespace Suggestion\Package\SecurityService;

class AlternativePowerSources extends BasePackage {
	public $alias = 'AlternativePowerSources';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Alternative Power Sources');

		$this->data = array(
			'name' => $this->name,
			'objective' => __("Have an alternative power source when available."),
			'security_service_type_id' => SECURITY_SERVICE_PRODUCTION,
			'classifications' => '',
			'user_id' => ADMIN_ID,
			'collaborator_id' => array(ADMIN_ID),
			'security_policy_id' => '',
			'documentation_url' => '',
			'audit_metric_description' => __("The UPS/Power Generator tests and maintenance must be performed successfully."),
			'audit_success_criteria' => __("100% Accuracy"),
			'audit_calendar' => array(
				1 => $this->getAuditDate()
			),
			'maintenance_metric_description' => __('Check oil and fuel levels; corect if necesary'),
			'opex' => $this->getDefaultOpex(),
			'capex' => $this->getDefaultCapex(),
			'resource_utilization' => 7
		);

		if (date("n") != 12) {
			$this->data['maintenance_calendar'] = $this->getMaintenanceDate();
		}
	}

	private function getMaintenanceDate() {
		$currentMonth = date("n");
		$currentDay = date("j");
		if ($currentDay > 27) {
			$currentDay = 27;
		}
		$december = 12;

		$dates = array();
		for ($i = ($currentMonth+1); $i <= 12; $i++) {
			$dates[] = array(
				'day' => $currentDay,
				'month' => $i
			);
		}

		return $dates;
	}
}
