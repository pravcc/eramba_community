<?php
App::uses('BaseFilterDashboardAttribute', 'AdvancedFilters.Lib/Dashboard/Attribute');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class DynamicFilterDashboardAttribute extends BaseFilterDashboardAttribute {
	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		parent::__construct($Dashboard, $DashboardKpiObject);
		
		$this->templates = [
			'recently_created' => [
				'title' => __('%s created during the past two weeks'),
				'params' => [
					'created' => [
						'value' => AbstractQuery::MINUS_14_DAYS_VALUE,
						'comparisonType' => AbstractQuery::COMPARISON_ABOVE
					]
				]
			],
			'recently_deleted' => [
				'title' => __('%s deleted during the past two weeks'),
				'softDelete' => false,
				'params' => [
					'deleted_date' => [
						'value' => AbstractQuery::MINUS_14_DAYS_VALUE,
						'comparisonType' => AbstractQuery::COMPARISON_ABOVE
					],
					'deleted' => [
						'value' => '1',
						'comparisonType' => AbstractQuery::COMPARISON_EQUAL
					]
				]
			]
		];

		
	}

}