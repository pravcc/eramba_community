<?php
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');

/**
 * Extended Cake Finder Lib.
 */
class AdvancedQueryBuilder
{

/**
 * AdvancedQuery instance.
 * 
 * @var AdvancedQuery
 */
	protected $_query = null;

/**
 * Construnct.
 * 
 * @param String|Model $model Model name or instance.
 * @param string $finder Finder type.
 * @param array $options Query options.
 */
	public function __construct($model, $finder = 'all', $options = []) {
		$this->_query = new AdvancedQuery($model, $finder, $options);
	}

/**
 * Set model.
 * 
 * @param String|Model Model name or instance.
 * @return AdvancedQueryBuilder
 */
	public function model($model) {
		$this->_query->model($model);

		return $this;
	}

/**
 * Set finder.
 * 
 * @param String Finder type.
 * @return AdvancedQueryBuilder
 */
	public function finder($finder) {
		$this->_query->finder($finder);

		return $this;
	}

/**
 * Set conditions.
 * 
 * @param Array Conditions.
 * @return AdvancedQueryBuilder
 */
	public function where($conditions) {
		$this->_query->where($conditions);

		return $this;
	}

/**
 * Set advanced conditions. 
 * @see AdvancedQuery::advancedWhere()
 * 
 * @param Array Conditions.
 * @param String|Model|Array Associated model or array of models if we need deeper association. 
 * @param boolean $notIn On true last condition will be replaced by NOT IN.
 * @return AdvancedQueryBuilder
 */
	public function advancedWhere($conditions, $model = null, $notIn = false) {
		$this->_query->advancedWhere($conditions, $model, $notIn);

		return $this;
	}

/**
 * Set select fields.
 * 
 * @param Array Fields.
 * @return AdvancedQueryBuilder
 */
	public function select($fields) {
		$this->_query->select($fields);

		return $this;
	}

/**
 * Set contain.
 * 
 * @param Array Contain.
 * @return AdvancedQueryBuilder
 */
	public function contain($contain) {
		$this->_query->contain($contain);

		return $this;
	}

/**
 * Set joins.
 * 
 * @param Array Joins.
 * @return AdvancedQueryBuilder
 */
	public function joins($joins) {
		$this->_query->joins($joins);

		return $this;
	}

/**
 * Set order.
 * 
 * @param Array Order.
 * @return AdvancedQueryBuilder
 */
	public function order($order) {
		$this->_query->order($order);

		return $this;
	}

/**
 * Set group.
 * 
 * @param Array Group.
 * @return AdvancedQueryBuilder
 */
	public function group($group) {
		$this->_query->group($group);

		return $this;
	}

/**
 * Set offset.
 * 
 * @param int Offset.
 * @return AdvancedQueryBuilder
 */
	public function offset($offset) {
		$this->_query->offset($offset);

		return $this;
	}

/**
 * Set page.
 * 
 * @param int Page.
 * @return AdvancedQueryBuilder
 */
	public function page($page) {
		$this->_query->page($page);

		return $this;
	}

/**
 * Set limit.
 * 
 * @param int Limit.
 * @return AdvancedQueryBuilder
 */
	public function limit($limit) {
		$this->_query->limit($limit);

		return $this;
	}

/**
 * Set recursive to query options.
 * 
 * @param int Recursive.
 * @return AdvancedQueryBuilder
 */
	public function recursive($recursive) {
		$this->_query->recursive($recursive);

		return $this;
	}

/**
 * Execute query.
 * 
 * @param String $finder Finder type.
 * @return array Array of data.
 */
	public function get($finder = null) {
		return $this->_query->get($finder);
	}

/**
 * Get query string.
 * 
 * @param String $finder Finder type.
 * @return String Query.
 */
	public function getQuery($finder = null) {
        return $this->_query->getQuery($finder);
	}

/**
 * Execute query with all finder.
 * 
 * @return array Array of data.
 */
	public function all() {
		return $this->_query->get('all');
	}

/**
 * Execute query with first finder.
 * 
 * @return array Array of data.
 */
	public function first() {
		return $this->_query->get('first');
	}

/**
 * Execute query with count finder.
 * 
 * @return int Count of result rows.
 */
	public function count() {
		return $this->_query->get('count');
	}

}