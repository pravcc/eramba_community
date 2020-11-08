<?php
App::uses('AppHelper', 'View/Helper');
App::uses('DataAsset', 'Model');
App::uses('SecurityService', 'Model');

class SecurityServicesHelper extends AppHelper 
{
	public $helpers = array('NotificationSystem', 'Html', 'Form', 'FormReload', 'FieldData.FieldData', 'Ux', 'Alerts');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function calendarTypeField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions([
			'field' => $Field
		]));
	}

	public function calendarRecurrenceStartDateField(FieldDataEntity $Field)
	{
		$out = "";

		$calendarType = 0;
		$countElemId = "";
		$fieldName = $Field->getFieldName();
		if ($fieldName === 'audit_calendar_recurrence_start_date') {
			$calendarType = $this->_View->get('auditCalendarType', 0);
			$countElemId = 'audit-recurrence-count';
		} elseif ($fieldName === 'maintenance_calendar_recurrence_start_date') {
			$calendarType = $this->_View->get('maintenanceCalendarType', 0);
			$countElemId = 'maintenance-recurrence-count';
		}
		
		if ($calendarType == SecurityService::CALENDAR_TYPE_RECURRENCE_DATE) {
			$out = $this->FieldData->input($Field, [
				'class' => [
					'audit-calendar-datepicker'
				],
				'data-jsui-dp-min-date' => date('Y', time()) . '-01-01',
				'data-jsui-dp-max-date' => date('Y', time()) . '-12-31',
				'data-yjs-request' => 'app/triggerRequest/#' . $countElemId,
				'data-yjs-event-on' => 'change',
				'data-yjs-use-loader' => 'false'
			]);
		}

		return $out;
	}

	public function calendarRecurrenceFrequencyField(FieldDataEntity $Field)
	{
		$out = "";

		$calendarType = 0;
		$countElemId = "";
		$fieldName = $Field->getFieldName();
		if ($fieldName === 'audit_calendar_recurrence_frequency') {
			$calendarType = $this->_View->get('auditCalendarType', 0);
			$countElemId = 'audit-recurrence-count';
		} elseif ($fieldName === 'maintenance_calendar_recurrence_frequency') {
			$calendarType = $this->_View->get('maintenanceCalendarType', 0);
			$countElemId = 'maintenance-recurrence-count';
		}

		if ($calendarType == SecurityService::CALENDAR_TYPE_RECURRENCE_DATE) {
			$out = $this->FieldData->input($Field, [
				'data-yjs-request' => 'app/triggerRequest/#' . $countElemId,
				'data-yjs-event-on' => 'change',
				'data-yjs-use-loader' => 'false'
			]);
		}

		return $out;
	}

	public function calendarRecurrencePeriodField(FieldDataEntity $Field)
	{
		$out = "";
		
		$calendarType = 0;
		$countElemId = "";
		$fieldName = $Field->getFieldName();
		if ($fieldName === 'audit_calendar_recurrence_period') {
			$calendarType = $this->_View->get('auditCalendarType', 0);
			$countElemId = 'audit-recurrence-count';
		} elseif ($fieldName === 'maintenance_calendar_recurrence_period') {
			$calendarType = $this->_View->get('maintenanceCalendarType', 0);
			$countElemId = 'maintenance-recurrence-count';
		}

		if ($calendarType == SecurityService::CALENDAR_TYPE_RECURRENCE_DATE) {
			$out = $this->FieldData->input($Field, [
				'data-yjs-request' => 'app/triggerRequest/#' . $countElemId,
				'data-yjs-event-on' => 'change',
				'data-yjs-use-loader' => 'false'
			]);

			$formName = $this->_View->get('formName');
			if ($fieldName === 'audit_calendar_recurrence_period') {
				$url = Router::url(['controller' => 'SecurityServices', 'action' => 'getRecurrenceDatesCount', 'audits']);
				$out .= $this->Alerts->info("<span id=\"audit-recurrence-count\" data-yjs-request=\"app/load\" data-yjs-event-on=\"init\" data-yjs-target=\"self\" data-yjs-server-url=\"post::{$url}\" data-yjs-forms=\"{$formName}\"></span>");
			} elseif ($fieldName === 'maintenance_calendar_recurrence_period') {
				$url = Router::url(['controller' => 'SecurityServices', 'action' => 'getRecurrenceDatesCount', 'maintenances']);
				$out .= $this->Alerts->info("<span id=\"maintenance-recurrence-count\" data-yjs-request=\"app/load\" data-yjs-event-on=\"init\" data-yjs-target=\"self\" data-yjs-server-url=\"post::{$url}\" data-yjs-forms=\"{$formName}\"></span>");
			}
		}

		return $out;
	}

	public function auditsField(FieldDataEntity $Field)
	{
		$out = "";
		
		$calendarType = 0;
		$fieldName = $Field->getFieldName();
		if ($fieldName === 'SecurityServiceAuditDate') {
			$calendarType = $this->_View->get('auditCalendarType', 0);
		} elseif ($fieldName === 'SecurityServiceMaintenanceDate') {
			$calendarType = $this->_View->get('maintenanceCalendarType', 0);
		}

		if ($calendarType == SecurityService::CALENDAR_TYPE_SPECIFIC_DATES) {
			$out .= $this->Ux->handleAuditCalendarFields($Field, [
				'controller' => 'securityServices'
			]);
		}

		return $out;
	}

	public function auditMetricDescriptionField(FieldDataEntity $Field)
	{
		$calendarType = $this->_View->get('auditCalendarType', 0);
		$disabled = $calendarType == SecurityService::CALENDAR_TYPE_NO_DATES ? true : false;
		return $this->FieldData->input($Field, [
			'disabled' => $disabled
		]);
	}

	public function auditSuccessCriteriaField(FieldDataEntity $Field)
	{
		$calendarType = $this->_View->get('auditCalendarType', 0);
		$disabled = $calendarType == SecurityService::CALENDAR_TYPE_NO_DATES ? true : false;
		return $this->FieldData->input($Field, [
			'disabled' => $disabled
		]);
	}

	public function auditOwnerField(FieldDataEntity $Field)
	{
		$calendarType = $this->_View->get('auditCalendarType', 0);
		$disabled = $calendarType == SecurityService::CALENDAR_TYPE_NO_DATES ? true : false;
		return $this->FieldData->input($Field, [
			'disabled' => $disabled
		]);
	}

	public function auditEvidenceOwnerField(FieldDataEntity $Field)
	{
		$calendarType = $this->_View->get('auditCalendarType', 0);
		$disabled = $calendarType == SecurityService::CALENDAR_TYPE_NO_DATES ? true : false;
		return $this->FieldData->input($Field, [
			'disabled' => $disabled
		]);
	}

	public function maintenanceMetricDescriptionField(FieldDataEntity $Field)
	{
		$calendarType = $this->_View->get('maintenanceCalendarType', 0);
		$disabled = $calendarType == SecurityService::CALENDAR_TYPE_NO_DATES ? true : false;
		return $this->FieldData->input($Field, [
			'disabled' => $disabled
		]);
	}

	public function maintenanceOwnerField(FieldDataEntity $Field)
	{
		$calendarType = $this->_View->get('maintenanceCalendarType', 0);
		$disabled = $calendarType == SecurityService::CALENDAR_TYPE_NO_DATES ? true : false;
		return $this->FieldData->input($Field, [
			'disabled' => $disabled
		]);
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->processItemArray($item, 'SecurityService');
		$statuses = array();
		
		if ($this->getAllowCond($allow, 'audits_last_passed') && !$item['SecurityService']['audits_last_passed']) {
			$statuses[$this->getStatusKey('audits_last_passed')] = array(
				'label' => __('Last audit failed'),
				'type' => 'danger'
			);
		}

		if ($this->getAllowCond($allow, 'audits_last_missing') && $item['SecurityService']['audits_last_missing']) {
			$statuses[$this->getStatusKey('audits_last_missing')] = array(
				'label' => __('Last audit missing'),
				'type' => 'warning'
			);
		}

		if ($this->getAllowCond($allow, 'maintenances_last_passed') && !$item['SecurityService']['maintenances_last_passed']) {
			$statuses[$this->getStatusKey('maintenances_last_passed')] = array(
				'label' => __('Last maintenance failed'),
				'type' => 'danger'
			);
		}

		if ($this->getAllowCond($allow, 'maintenances_last_missing') && $item['SecurityService']['maintenances_last_missing']) {
			$statuses[$this->getStatusKey('maintenances_last_missing')] = array(
				'label' => __('Last maintenance missing'),
				'type' => 'warning'
			);
		}

		if ($this->getAllowCond($allow, 'ongoing_corrective_actions') && $item['SecurityService']['ongoing_corrective_actions']) {
			$statuses[$this->getStatusKey('ongoing_corrective_actions')] = array(
				'label' => __('Ongoing Corrective Actions'),
				'type' => 'improvement'
			);
		}

		if ($this->getAllowCond($allow, 'security_service_type_id') && $item['SecurityService']['security_service_type_id'] == SECURITY_SERVICE_DESIGN) {
			$statuses[$this->getStatusKey('security_service_type_id')] = array(
				'label' => __('Control in Design'),
				'type' => 'warning'
			);
		}

		if ($this->getAllowCond($allow, 'control_with_issues') && $item['SecurityService']['control_with_issues']) {
			$statuses[$this->getStatusKey('control_with_issues')] = array(
				'label' => __('Control with Issues'),
				'type' => 'danger'
			);
		}

		$inherit = array(
			'SecurityIncidents' => array(
				'model' => 'SecurityIncident',
				'config' => array('ongoing_incident')
			),
			'Projects' => array(
				'model' => 'Projects',
				'config' => array('expired')
			),
		);
		
		if ($this->getAllowCond($allow, INHERIT_CONFIG_KEY)) {
			$statuses = am($statuses, $this->getInheritedStatuses($item, $inherit));
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->processStatusOptions($options);

		$statuses = $this->getStatusArr($item, $options['allow']);
		return $this->styleStatuses($statuses, $options);
	}

	/**
	 * Returns status labels for audit and maintenance statuses.
	 *
	 */
	public function getStatusLabels($item, $includeType = false) {
		$msg = array();
		if (/*$includeType && */$item['security_service_type_id'] == SECURITY_SERVICE_DESIGN) {
			$msg[] = $this->Html->tag('span', __('Status is Design'), array('class' => 'label label-danger'));
		}
		else {
			if (!$item['audits_all_done']) {
				$msg[] = $this->Html->tag('span', __('Missing audits'), array('class' => 'label label-warning'));

			}
			if (!$item['audits_last_passed']) {
				$msg[] = $this->Html->tag('span', __('Last audit failed'), array('class' => 'label label-danger'));
			}
			if ($item['audits_improvements']) {
				$msg[] = $this->Html->tag('span', __('Being fixed'), array('class' => 'label label-primary'));
			}
			if ($item['audits_all_done'] && $item['audits_last_passed']) {
				$msg[] = $this->Html->tag('span', __('No audit issues'), array('class' => 'label label-success'));
			}

			if (!$item['maintenances_all_done']) {
				$msg[] = $this->Html->tag('span', __('Missing maintenances'), array('class' => 'label label-warning'));

			}
			if (!$item['maintenances_last_passed']) {
				$msg[] = $this->Html->tag('span', __('Last maintenance failed'), array('class' => 'label label-danger'));
			}
			if ($item['maintenances_all_done'] && $item['maintenances_last_passed']) {
				$msg[] = $this->Html->tag('span', __('No maintenance issues'), array('class' => 'label label-success'));
			}
		}

		return $msg;
	}

	public function statusLabels($item, $includeType = false, $implodeGlue = '<br>') {
		$labels = $this->getStatusLabels($item, $includeType);

		echo implode($implodeGlue, $labels);
	}

	/**
	 * Returns status labels for audit and maintenance statuses that has issues.
	 *
	 */
	public function getIssueStatusLabels($items) {
		$msg = array();

		$failedAudit = $missingAudit = $failedMaintenance = $missingMaintenance = false;
		foreach ($items as $item) {
			if (!$failedAudit && !$item['audits_last_passed']) {
				$msg[] = $this->Html->tag('span', __('Failed audit'), array('class' => 'label label-danger'));
				$failedAudit = true;
			}
			if (!$missingAudit && !$item['audits_all_done']) {
				$msg[] = $this->Html->tag('span', __('Missing audit'), array('class' => 'label label-warning'));
				$missingAudit = true;
			}

			if (!$failedMaintenance && !$item['maintenances_last_passed']) {
				$msg[] = $this->Html->tag('span', __('Failed maintenance'), array('class' => 'label label-danger'));
				$failedMaintenance = true;
			}
			if (!$missingMaintenance && !$item['maintenances_all_done']) {
				$msg[] = $this->Html->tag('span', __('Missing maintenance'), array('class' => 'label label-warning'));
				$missingMaintenance = true;
			}
		}

		return $msg;
	}

	public function issueStatusLabels($items, $implodeGlue = '<br>') {
		$labels = $this->getIssueStatusLabels($items);

		echo implode($implodeGlue, $labels);
	}

	/**
	 * Function is used by another section helpers.
	 */
	public static function complianceItemsList($Item)
	{
		$list = '';

		if (!empty($Item->ComplianceManagement)) {
			foreach ($Item->ComplianceManagement as $ComplianceManagement) {
				$thirdPartyName = '';
				$itemId = '';
				$itemName = '';

				if (!empty($ComplianceManagement->CompliancePackageItem)) {
					$itemId = $ComplianceManagement->CompliancePackageItem->item_id;
					$itemName = $ComplianceManagement->CompliancePackageItem->name;
				}

				if (!empty($ComplianceManagement->CompliancePackageItem)
					&& !empty($ComplianceManagement->CompliancePackageItem->CompliancePackage)
					&& !empty($ComplianceManagement->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator)
				) {
					$thirdPartyName = $ComplianceManagement->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator->name;
				}

				$list .= sprintf('<li>%s / %s / %s</li>', $thirdPartyName, $itemId, $itemName);
			}
		}

		if (!empty($list)) {
			$list = '<ul>' . $list . '</ul>';
		}

		return $list;
	}

	/**
	 * Function is used by another section helpers.
	 */
	public static function riskItemsList($Item)
	{
		$list = '';

		$risks = [
			'Risk' => __('Asset Risk'),
			'ThirdPartyRisk' => __('Third Party Risk'),
			'BusinessContinuity' => __('Business Risk'),
		];

		foreach ($risks as $model => $modelLabel) {
			foreach ($Item->{$model} as $RiskItem) {
				$list .= sprintf('<li>%s / %s </li>', $modelLabel, $RiskItem->title);
			}
		}

		if (!empty($list)) {
			$list = '<ul>' . $list . '</ul>';
		}

		return $list;
	}

	/**
	 * Function is used by another section helpers.
	 */
	public static function dataAssetItemsList($Item)
	{
		$list = '';

		foreach ($Item->DataAsset as $DataAssetItem) {
			$assetName = '';
			if (!empty($DataAssetItem->DataAssetInstance) && !empty($DataAssetItem->DataAssetInstance->Asset)) {
				$assetName = $DataAssetItem->DataAssetInstance->Asset->name;
			}

			$list .= sprintf('<li>%s / %s / %s</li>', $assetName, DataAsset::statuses($DataAssetItem->data_asset_status_id), $DataAssetItem->title);
		}

		if (!empty($list)) {
			$list = '<ul>' . $list . '</ul>';
		}

		return $list;
	}

}
