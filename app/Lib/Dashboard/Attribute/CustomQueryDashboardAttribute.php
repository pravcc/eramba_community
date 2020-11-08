<?php
App::uses('TemplatesDashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');

/**
 * Class makes it possible to use customized queries as Dashboard KPIs.
 */
class CustomQueryDashboardAttribute extends TemplatesDashboardAttribute {
	public $templateClassName = 'CustomQueryDashboardTemplate';

	public function __construct() {
		$scores = [
			'total_risk_score' => [
				'title' => __('Current Total Risk Score'),
				'query' => [
					'fields' => [
						'SUM(%s.risk_score) as count'
					],
					'recursive' => -1
				]
			],
			'total_residual_score' => [
				'title' => __('Current Total Residual Score'),
				'query' => [
					'fields' => [
						'SUM(%s.residual_risk) as count'
					],
					'recursive' => -1
				]
			]
		];

		$this->templates = [
			'Risk' => $scores,
			'ThirdPartyRisk' => $scores,
			'BusinessContinuity' => $scores
		];
	}

	/**
	 * Generic method that handles query for attribute classes built on top of advanced filters.
	 * It requires that method buildParams() is defined and returns correctly formatted filter parameters.
	 * 
	 * @param  Model  $Model     Model.
	 * @param  string $attribute Attribute value.
	 * @return array             Query parameters for find operations.
	 */
	public function buildQuery(Model $Model, $attribute) {
		$query = $this->getQuery($Model, $attribute);
		
		foreach ($query['fields'] as &$field) {
			$field = sprintf($field, $Model->alias);
		}

		return $query;
	}

	/**
	 * Method should convert provided $attribute into parameters that are compatible with Advanced Filters.
	 * 
	 * @param  Model  $Model     Model.
	 * @param  string $attribute Attribute value.
	 * @return array             Array of compatible parameters
	 * @see    FilterField       Class in AdvancedFilters module.
	 */
	public function getQuery(Model $Model, $attribute) {
		$TemplateInstance = $this->templateInstance($Model, $attribute);

		return $TemplateInstance->get('query');
	}

	public function listAttributes(Model $Model) {
		if (isset($this->templates[$Model->alias])) {
			return array_keys($this->templates[$Model->alias]);
		}

		return [];
	}

	public function templateInstance(Model $Model, $path) {
		$path = $Model->alias . '.' . $path;
		return parent::templateInstance($Model, $path);
	}

}