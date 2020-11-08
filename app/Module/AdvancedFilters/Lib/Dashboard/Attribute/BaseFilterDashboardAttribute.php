<?php
App::uses('TemplatesDashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');
App::uses('FilterField', 'AdvancedFilters.Lib');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');

/**
 * Abstract class to make solid base in Advanced Filters to make use of Dashboard Attributes.
 */
abstract class BaseFilterDashboardAttribute extends TemplatesDashboardAttribute {
	public $templateClassName = 'AdvancedFilters.FilterParamsDashboardTemplate';

	/**
	 * Builds the url for a template.
	 */
	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		$TemplateInstance = $this->templateInstance($Model, $attribute);

		$findOn = $TemplateInstance->findOn($Model);

		foreach ($TemplateInstance->get('params') as $field => $param) {
			$filterField = $this->mapFilterField($Model, $field);
			$FilterFieldInstance = new FilterField($findOn, $filterField, []);

			$tmpParams = $param;
			$FilterFieldInstance->config($tmpParams);

			$query = array_merge($query, $FilterFieldInstance->buildQueryParams());
		}

		return $query;
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
		$TemplateInstance = $this->templateInstance($Model, $attribute);

		$findOn = $TemplateInstance->findOn($Model);
		$findField = $TemplateInstance->findField($Model);

		$_filter = new AdvancedFiltersObject();
		$_filter->setModel($findOn);
		$_filter->setFilterValues([]);
		$parameters = $this->buildParams($Model, $attribute);
		$_filter->setConvertedValues($parameters);
		$conditions = $_filter->getConditions();

		$query = [
			'conditions' => $conditions,
			'fields' => [$findOn->escapeField($findField)]
		];

		if ($Model->alias !== $findOn->alias) {
			$query['recursive'] = -1;
			$primaryField = $Model->escapeField($Model->primaryKey);

			$query = $findOn->getQuery('all', $query);
			$query = [
				'conditions' => [
					"{$primaryField} IN ({$query})"
				],
				'fields' => [$primaryField]
			];
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
	public function buildParams(Model $Model, $attribute) {
		$TemplateInstance = $this->templateInstance($Model, $attribute);

		return $TemplateInstance->get('params');
	}

}