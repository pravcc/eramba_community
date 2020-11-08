<?php
App::uses('AdminDashboardAttribute', 'Lib/Dashboard/Attribute');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('Project', 'Model');
App::uses('FilterField', 'AdvancedFilters.Lib');

class UserDashboardAttribute extends AdminDashboardAttribute {

	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		$this->totalTitle = __('Total');
		parent::__construct($Dashboard, $DashboardKpiObject);


		$this->templates['ComplianceAnalysisFinding'] = [
			'expired' => [
				'title' => __('Expired'),
				'params' => [
					'status' => [
						'value' => POLICY_EXCEPTION_CLOSED,
						'comparisonType' => AbstractQuery::COMPARISON_NOT_EQUAL
					],
					'due_date' => [
						'value' => AbstractQuery::TODAY_VALUE,
						'comparisonType' => AbstractQuery::COMPARISON_UNDER
					]
				]
			], 
			'open' => [
				'title' => __('Open'),
				'params' => [
					'status' => [
						'value' => POLICY_EXCEPTION_OPEN,
						'comparisonType' => AbstractQuery::COMPARISON_EQUAL
					]
				]
			], 
			'closed' => [
				'title' => __('Closed'),
				'params' => [
					'status' => [
						'value' => POLICY_EXCEPTION_CLOSED,
						'comparisonType' => AbstractQuery::COMPARISON_EQUAL
					]
				]
			]
		];

	}

}