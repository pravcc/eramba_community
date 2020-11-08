<?php
App::uses('BaseFilterDashboardAttribute', 'AdvancedFilters.Lib/Dashboard/Attribute');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('ComplianceTreatmentStrategy', 'Model');

class ComplianceTypeDashboardAttribute extends BaseFilterDashboardAttribute {

	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		parent::__construct($Dashboard, $DashboardKpiObject);

		$this->templates = [
			'total' => [
				'title' => __('Total'),
				'params' => [
				]
			],
			'compliant' => [
				'title' => __('Compliant'),
				'params' => [
					'compliance_treatment_strategy_id' => [
						'value' => [ComplianceTreatmentStrategy::STRATEGY_COMPLIANT],
						'comparisonType' => AbstractQuery::COMPARISON_IN
					]
				]
			],
			'overlooked' => [
				'title' => __('Overlooked'),
				'params' => [
					'compliance_treatment_strategy_id' => [
						'value' => [null],
						'comparisonType' => AbstractQuery::COMPARISON_IS_NULL
					]
				]
			],
			'not_applicable' => [
				'title' => __('Not Applicable'),
				'params' => [
					'compliance_treatment_strategy_id' => [
						'value' => [ComplianceTreatmentStrategy::STRATEGY_NOT_APPLICABLE],
						'comparisonType' => AbstractQuery::COMPARISON_IN
					]
				]
			],
			'non_compliant' => [
				'title' => __('Non compliant'),
				'params' => [
					'compliance_treatment_strategy_id' => [
						'value' => [ComplianceTreatmentStrategy::STRATEGY_NOT_COMPLIANT],
						'comparisonType' => AbstractQuery::COMPARISON_IN
					]
				]
			],
			'control_missing_audits' => [
				'title' => __('Controls missing audits'),
				'params' => [
					'ObjectStatus_security_service_audits_last_missing' => [
						'value' => true,
						'comparisonType' => AbstractQuery::COMPARISON_EQUAL
					]
				]
			],
			'control_failed_audits' => [
				'title' => __('Controls failed audits'),
				'params' => [
					'ObjectStatus_security_service_audits_last_not_passed' => [
						'value' => true,
						'comparisonType' => AbstractQuery::COMPARISON_EQUAL
					]
				]
			],
			'policy_missing_reviews' => [
				'title' => __('Policies missing reviews'),
				'params' => [
					'ObjectStatus_security_policy_expired_reviews' => [
						'value' => true,
						'comparisonType' => AbstractQuery::COMPARISON_EQUAL
					]
				]
			],
		];
	}


}