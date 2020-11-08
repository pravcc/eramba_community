<?php
App::uses('FilterCase', 'AdvancedFilters.Lib/QueryAdapter/FilterCase');
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('QueryCondition', 'AdvancedQuery.Lib/Template');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

/**
 * Extension of base filter case processor.
 */
class MultipleCase extends FilterCase
{

/**
 * Scope matching params. 
 * Define scope params that must be in match with input params (defined in _params) to trigger of this case processor.
 * 
 * @var array
 */
	protected $_matchingParams = [
		'comparisonType' => [
			FilterAdapter::COMPARISON_IN, FilterAdapter::COMPARISON_NOT_IN, FilterAdapter::COMPARISON_ALL_IN,
			FilterAdapter::COMPARISON_NOT_ALL_IN, FilterAdapter::COMPARISON_ONLY_IN, FilterAdapter::COMPARISON_NOT_ONLY_IN
		]
	];

/**
 * Adapt query for this case. Build conditions and do whatever you need to adapt query.
 * 
 * @param AdvancedQuery $query Query instance.
 * @return AdvancedQuery
 */
	protected function _adaptQuery($query) {
		$allInTypes = [FilterAdapter::COMPARISON_ALL_IN, FilterAdapter::COMPARISON_NOT_ALL_IN];
		$onlyInTypes = [FilterAdapter::COMPARISON_ONLY_IN, FilterAdapter::COMPARISON_NOT_ONLY_IN];

		$valueGroups = [$this->_params['findValue']];

		if (in_array($this->_params['comparisonType'], $allInTypes) || in_array($this->_params['comparisonType'], $onlyInTypes)) {
			$valueGroups = [];
			//we need to separate values to single comparisons
			foreach ($this->_params['findValue'] as $value) {
				$valueGroups[] = [$value];
			}
		}

		foreach ($valueGroups as $value) {
			$query->advancedWhere([
				QueryCondition::comparison(
					$this->_params['findField'],
					FilterAdapter::$_comparisonSign[$this->_params['comparisonType']],
					(array) $value
				)
			], $this->_params['findFieldModel']);
		}

		//on only comparison we need to create query to check if only these values are assigned to our item
		if (in_array($this->_params['comparisonType'], $onlyInTypes)) {
			$query->advancedWhere([
				QueryCondition::comparison(
					$this->_params['findField'],
					'NOT IN',
					(array) $this->_params['findValue']
				)
			], $this->_params['findFieldModel'], true);
		}

		return $query;
	}
}