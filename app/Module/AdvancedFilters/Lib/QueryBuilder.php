<?php
// namespace AdvancedFilters\Lib;

class QueryBuilder {

	protected $_query = [];

	public function __construct(Model $Model) {
		$this->_model = $Model;
	}

	public function find($type = 'all') {

	}

	public function addCondition($condition) {
		$this->_query['conditions'] = Hash::merge($this->_query['conditions'], $condition);

		return $this->_query['conditions'];
	}

	public function addFragment($fragment) {
		if (!$fragment instanceof AbstractQueryFragment) {
			throw new Exception('Trying to add non-compatible fragment to the query.', 1);
		}

		if ($fragment instanceof SubQueryFragment) {
			$subquery = sprintf('%s IN (%s)', $this->_model->escapeField(), $fragment->getFragment());
			$this->_query['conditions'][] = $subquery;
		}
	}

	public function getQuery() {
		return $this->_query;
	}

	public function mergeQuery(QueryBuilder $QueryBuilder) {
		return $this->_query = Hash::merge($this->_query, $QueryBuilder->getQuery());
	}

}
