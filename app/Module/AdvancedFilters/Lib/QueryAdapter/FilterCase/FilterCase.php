<?php
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('QueryCondition', 'AdvancedQuery.Lib/Template');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

/**
 * Base filter case processor.
 * Check if filter params are in match with this processor scopes (defined in _matchingParams) 
 * and than create and adapt query for our case.
 */
class FilterCase
{

/**
 * Scope matching params. 
 * Define scope params that must be in match with input params (defined in _params) to trigger of this case processor.
 * 
 * @var array
 */
	protected $_matchingParams = [];

/**
 * Input params.
 * 
 * @var array
 */
	protected $_params = [];

/**
 * Determines if adapt query propagation have to be stopped.
 * 
 * @var boolean
 */
	protected $_stopPropagation = true;

	public function __construct($params) {
		$this->_params = $params;
	}

/**
 * Set and get stopPropagation property.
 * 
 * @var boolean
 */
	public function stopPropagation($stopPropagation = null) {
		if ($stopPropagation !== null) {
			$this->_stopPropagation = $stopPropagation;
		}

		return $this->_stopPropagation;
	}

/**
 * Check if scope matching params are in match with input params.
 * 
 * @return boolean Scope match or not.
 */
	public function match() {
		$ret = true;

		foreach ($this->_matchingParams as $param => $values) {
			if ($values !== false && (!isset($this->_params[$param]) || !in_array($this->_params[$param], $values))) {
				$ret = false;
			}
		}

		return $ret;
	}

/**
 * Create and return adapt query to input params/conditions.
 * 
 * @return AdvancedQuery Query.
 */
	public function adaptQuery($query) {
        $this->_adaptQuery($query);

        return $query;
	}

/**
 * Adapt query for this case. Build conditions and do whatever you need to adapt query.
 * 
 * @param AdvancedQuery $query Query instance.
 * @return AdvancedQuery
 */
	protected function _adaptQuery($query) {
		$modelPath = null;
		if (is_array($this->_params['findFieldModel']) && count($this->_params['findFieldModel']) >= 2) {
			$modelPath = $this->_params['findFieldModel'];
		}

		$query->advancedWhere([
			QueryCondition::comparison(
				$this->_params['findField'],
				FilterAdapter::$_comparisonSign[$this->_params['comparisonType']],
				$this->_params['findValue']
			),
			$this->_customFieldCondition()
		], $modelPath);

		return $query;
	}

/**
 * Get additional condition for custom fieds.
 * 
 * @return string|null Condition
 */
    protected function _customFieldCondition() {
    	$condition = null;

    	if ($this->_params['findFieldModel'] == 'CustomFieldValue') {
    		$condition = 'CustomFieldValue.custom_field_id = ' . $this->_params['filter']['customField'];
    	}

    	return $condition;
    }
}