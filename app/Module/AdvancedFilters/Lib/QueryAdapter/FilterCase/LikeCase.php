<?php
App::uses('FilterCase', 'AdvancedFilters.Lib/QueryAdapter/FilterCase');
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('QueryCondition', 'AdvancedQuery.Lib/Template');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

/**
 * Extension of base filter case processor.
 */
class LikeCase extends FilterCase
{

/**
 * Scope matching params. 
 * Define scope params that must be in match with input params (defined in _params) to trigger of this case processor.
 * 
 * @var array
 */
	protected $_matchingParams = [
		'comparisonType' => [FilterAdapter::COMPARISON_LIKE, FilterAdapter::COMPARISON_NOT_LIKE]
	];

/**
 * Adapt query for this case. Build conditions and do whatever you need to adapt query.
 * 
 * @param AdvancedQuery $query Query instance.
 * @return AdvancedQuery
 */
	protected function _adaptQuery($query) {
		$query->advancedWhere([
			QueryCondition::like(
				$this->_params['findField'],
				$this->_params['findValue']
			),
			$this->_customFieldCondition()
		], $this->_params['findFieldModel']);

		return $query;
	}
}