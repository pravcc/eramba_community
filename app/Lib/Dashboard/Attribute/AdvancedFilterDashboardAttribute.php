<?php
App::uses('BaseFilterDashboardAttribute', 'AdvancedFilters.Lib/Dashboard/Attribute');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');

class AdvancedFilterDashboardAttribute extends BaseFilterDashboardAttribute {

	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		return $query;
	}

	/**
	 * Convert advanced filter values list of params pulled from database into Dashboard-compatible format.
	 * 
	 * @param  array  $filterId Advanced Filter values array in a raw state.
	 * @return array            Converted array.
	 */
// 	public function buildParams(Model $Model, $filterId) {
// 		return [];
// 		$_filter = new AdvancedFiltersObject($filterId);
// 		$_filter->configureConditions();

// 		ddd($_filter->getConvertedValues());
// 		// $_filter->setModel($Model);
// 		// $_filter->setFilterValues([]);
// 		// $parameters = $this->buildParams($Model, $attribute);
// 		ddd($parameters);
// 		$_filter->setConvertedValues($parameters);

// 		$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
// 		$query = $AdvancedFilter->getFormattedValues($filterId);
// ddd($query);
// 		unset($query['advanced_filter']);
// 		// unset($query['_limit']);

// 		$newParams = [];
// 		foreach ($query as $param => $value) {
// 			if (strpos($param, '__') === false) {
// 				$newParams[$param]['value'] = $value;
// 				continue;
// 			}

// 			if (strpos($param, '__comp_type') !== false) {
// 				$field = explode('__', $param);
// 				$newParams[$field[0]]['comparisonType'] = $value;	
// 			}
// 		}

// 		return $newParams;
// 	}

	public function buildQuery(Model $Model, $attribute) {
		// ddd($attribute);
		// $Model->Behaviors->load('AdvancedFilters.AdvancedFilters');

		$_filter = new AdvancedFiltersObject($attribute);

		// $_filter->setModel($Model);
		// $_filter->setFilterValues([]);
		// $parameters = $this->buildParams($Model, $attribute);
		// $_filter->setConvertedValues($parameters);
		$conditions = $_filter->getConditions();
		// $findOptions = $_filter->parseFindOptions();

		$query = [
			'conditions' => $conditions,
			'fields' => [$Model->escapeField($Model->primaryKey)]
		];

		// $query = array_merge($query, $findOptions);

		return $query;
	}

	public function softDelete(Model $Model, $attribute) {
		return $this->softDelete;
	}

}
