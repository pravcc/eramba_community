<?php
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('ComplianceTreatmentStrategy', 'Model');

class ComplianceManagementItemCollection extends ItemDataCollection
{
	public function __construct(Model $Model)
	{
		parent::__construct($Model);
	}

	public function packageByTreatmentStrategy()
	{
		$data = [
			'yAxis' => $this->getThirdParties(),
			'label' => [
				__('Compliant'), __('Not Compliant'), __('Not Applicable'), __('Overlooked'), __('Improvement Project Expired'),
				__('Improvement Project with Expired Tasks'), __('Policy Missing Review'), __('Expired'), __('Last Audit failed'),
				__('Last audit missing'), __('Last maintenance missing'), __('Control with Issues'),
				__('Risk Review Expired'), __('Third Party Risk Review Expired'), __('Business Risk Review Expired'),
			],
			'stack' => [
				'1', '1', '1', '1', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2'
			],
			'data' => [
				$this->_treatmentStrategyCount(ComplianceTreatmentStrategy::STRATEGY_COMPLIANT),
				$this->_treatmentStrategyCount(ComplianceTreatmentStrategy::STRATEGY_NOT_COMPLIANT),
				$this->_treatmentStrategyCount(ComplianceTreatmentStrategy::STRATEGY_NOT_APPLICABLE),
				$this->_treatmentStrategyCount(null),
				$this->_statusCount('project_expired'),
				$this->_statusCount('project_expired_task'),
				$this->_statusCount('security_policy_expired_reviews'),
				$this->_statusCount('compliance_exception_expired'),
				$this->_statusCount('security_service_audits_last_not_passed'),
				$this->_statusCount('security_service_audits_last_missing'),
				$this->_statusCount('security_service_maintenances_last_missing'),
				$this->_statusCount('security_service_control_with_issues'),
				$this->_statusCount('risk_expired_reviews'),
				$this->_statusCount('third_party_risk_risk_above_appetite'),
				$this->_statusCount('business_continuity_expired_reviews'),
			]
		];

		return $data;
	}

	protected function _treatmentStrategyCount($value)
	{
		$data = $this->_getCompliancePackageRegulatorEmptyArray();

		foreach ($this as $Item) {
			if ($Item->compliance_treatment_strategy_id == $value) {
				$data[$Item->getCompliancePackageRegulatorId()]++;
			}
		}

		return $data;
	}

	protected function _statusCount($status)
	{
		$data = $this->_getCompliancePackageRegulatorEmptyArray();

		foreach ($this as $Item) {
			if ($Item->getStatusValue($status)) {
				$data[$Item->getCompliancePackageRegulatorId()]++;
			}
		}

		return $data;
	}

	protected function _getCompliancePackageRegulatorEmptyArray()
	{
		$regulators = $this->getCompliancePackageRegulators();

		foreach ($regulators as $key => $item) {
			$regulators[$key] = 0;
		}

		return $regulators;
	}

	public function getCompliancePackageRegulators()
	{
		$regulators = [];

		foreach ($this as $Item) {
			$regulators[$Item->getCompliancePackageRegulatorId()] = $Item->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator->name;
		}

		return $regulators;
	}
}