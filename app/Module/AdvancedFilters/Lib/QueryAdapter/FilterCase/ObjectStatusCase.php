<?php
App::uses('FilterCase', 'AdvancedFilters.Lib/QueryAdapter/FilterCase');
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('QueryCondition', 'AdvancedQuery.Lib/Template');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

/**
 * Extension of base filter case processor.
 */
class ObjectStatusCase extends FilterCase
{

/**
 * Scope matching params. 
 * Define scope params that must be in match with input params (defined in _params) to trigger of this case processor.
 * 
 * @var array
 */
	protected $_matchingParams = [
		'type' => [
			'object_status'
		],
	];

/**
 * Adapt query for this case. Build conditions and do whatever you need to adapt query.
 * 
 * @param AdvancedQuery $query Query instance.
 * @return AdvancedQuery
 */
	protected function _adaptQuery($query) {
		$ObjectStatus = ClassRegistry::init('ObjectStatus.ObjectStatus');

		list($model, $field) = pluginSplit($this->_params['findField']);

		$statusQuery = new AdvancedQuery($ObjectStatus, 'first', [
			'conditions' => [
				'ObjectStatus.name' => $field,
				'ObjectStatus.model' => $this->_params['model']->alias,
				'ObjectStatus.status' => $this->_params['findValue']
			],
			'fields' => ['ObjectStatus.foreign_key']
		]);

		$query->where(["{$this->_params['model']->alias}.{$this->_params['model']->primaryKey} IN ($statusQuery)"]);

		return $query;
	}
}