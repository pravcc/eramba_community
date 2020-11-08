<?php
App::uses('AppHelper', 'View/Helper');
App::uses('SecurityServicesHelper', 'View/Helper');

class SecurityServiceAuditsHelper extends AppHelper {
	public $helpers = array('NotificationSystem', 'Html', 'Eramba', 'FieldData.FieldData', 'FormReload');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function securityServiceField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}

	public function getStatuses($item, $addModelKey = false) {
		$statuses = array();

		$item = $this->Eramba->processItemArray($item, $addModelKey);

		$statuses = array_merge($statuses, $this->NotificationSystem->getStatuses($item));

		return $this->Eramba->processStatuses($statuses);
	}

	/**
	 * Is audit missing or not based on result and planned date.
	 */
	private function isMissing($item) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		$plannedDate = $item['SecurityServiceAudit']['planned_date'];

		return $plannedDate < $today;

		// $now = strtotime('now');
		// $plannedDate = strtotime($item['SecurityServiceAudit']['planned_date']);

		// return $plannedDate < $now;
	}

	public function getWidgetHeaderClass($item) {
		$widgetClass = false;
		if ($item['SecurityServiceAuditImprovement']['id'] != null) {
			$widgetClass = 'widget-status improvement';
		}
		else {
			if ($item['SecurityServiceAudit']['result'] === null && $this->isMissing($item)) {
				$widgetClass = 'widget-status warning';
			}
			elseif (in_array($item['SecurityServiceAudit']['result'], [AUDIT_FAILED, (string) AUDIT_FAILED], true)) {
				$widgetClass = 'widget-status danger';
			}
		}

		return $widgetClass;
	}

	/**
	 * Get item's widget header for an accordion.
	 */
	public function getWidgetHeader($item) {
		if ($item['SecurityServiceAudit']['result'] === null) {
			$brackets = __('Not due');
			if ($this->isMissing($item)) {
				$brackets = __('Missing');
			}
		}
		else {
			$statuses = getAuditStatuses();
			$brackets = $statuses[$item['SecurityServiceAudit']['result']];

			if ($item['SecurityServiceAuditImprovement']['id'] != null) {
				$brackets = sprintf('%s - %s', $brackets, __('Improvement'));
			}
		}

		return sprintf('%s (%s)', $item['SecurityServiceAudit']['planned_date'], $brackets);
	}

	/**
	 * result field output filter
	 */
	public function outputResult($data, $options = array()) {
		$statuses = getAuditStatuses();
		$value = '';

		if ($data === null || $data === false) {
			$value = __('Incomplete');
		}
		else {
			$value = $statuses[$data];
		}

		return $value;
	}

	public static function complianceItemsList($Item)
	{
		return SecurityServicesHelper::complianceItemsList($Item->SecurityService);
	}

	public static function riskItemsList($Item)
	{
		return SecurityServicesHelper::riskItemsList($Item->SecurityService);
	}

	public static function dataAssetItemsList($Item)
	{
		return SecurityServicesHelper::dataAssetItemsList($Item->SecurityService);
	}
}