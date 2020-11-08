<?php
App::uses('ClassRegistry', 'Utility');
App::uses('QueryTemplate', 'AdvancedQuery.Lib/Template');
App::uses('AdvancedQueryBuilder', 'AdvancedQuery.Lib');
App::uses('AdvancedQueryResult', 'AdvancedQuery.Lib');

/**
 * Extended Cake Finder Lib.
 */
class AdvancedQuery
{

/**
 * Related model instance.
 * 
 * @var Model
 */
	protected $_Model = null;

/**
 * Finder type (all|first|count|neighbors|list|threaded).
 * @see Model::find() $type parameter.
 * 
 * @var String
 */
	protected $_finder = null;

/**
 * Finder options.
 * @see Model::find() $query parameter.
 * 
 * @var array
 */
	protected $_queryOptions = [
		'conditions' => [],
		'fields' => [],
		'contain' => null,
		'joins' => [],
		'order' => [],
		'group' => [],
		'offset' => null,
		'page' => null,
		'limit' => null,
		'recursive' => null,

		'raw' => true 
	];

/**
 * Construnct.
 * 
 * @param String|Model $model Model name or instance.
 * @param string $finder Finder type.
 * @param array $options Query options.
 */
	public function __construct($model, $finder = 'all', $options = []) {
		$this->model($model);
		$this->finder($finder);
		$this->options($options);
	}

/**
 * Transform Query object to string.
 * 
 * @return string Query string.
 */
	public function __toString() {
		return $this->getQuery();
	}

/**
 * Set and get model.
 * 
 * @param String|Model Model name or instance.
 * @return Model
 */
	public function model($model = null) {
		if ($model !== null) {
			$this->_Model = self::getModelInstance($model);
		}

		return $this->_Model;
	}

/**
 * Set and get finder.
 * 
 * @param String Finder type.
 * @return String
 */
	public function finder($finder = null) {
		if ($finder !== null) {
			$this->_finder = $finder;
		}

		return $this->_finder;
	}

/**
 * Set and get query options.
 * 
 * @param Array Query options.
 * @return Array
 */
	public function options($options = null) {
		if ($options !== null) {
			$this->_queryOptions = array_merge($this->_queryOptions, $options);
		}
		
		return $this->_queryOptions;
	}

/**
 * Set conditions to query options.
 * 
 * @param Array Conditions.
 * @return void
 */
	public function where($conditions = []) {
		$this->_queryOptions['conditions'] = array_merge($this->_queryOptions['conditions'], $conditions);
	}

/**
 * Automated conditions builder for deeper conditions on associations.
 * If we have deeper association array of models needs to be ordered like assciations are defined.
 * 
 * @param Array Conditions.
 * @param String|Model|Array Associated model or array of models if we need deeper association. 
 * @param boolean $notIn On true last condition will be replaced by NOT IN.
 * @return void
 */
	public function advancedWhere($conditions = [], $model = null, $notIn = false) {
		if ($model === null && is_array($conditions)) {
			$model = $this->_findModelInConditions($conditions);
		}

		if (empty($model) || $model == $this->model()->alias) {
			return $this->where($conditions);
		}

		$assocPath = array_merge([$this->model()->alias], (array) $model);

		for ($i = (count($assocPath)-1); $i >= 1; $i--) {
			$LeftModel = self::getModelInstance($assocPath[$i-1]);
			$RightModel = (!empty($LeftModel->{$assocPath[$i]})) ? $LeftModel->{$assocPath[$i]} : self::getModelInstance($assocPath[$i]);

			$subQuery = $this->assocSubQuery($LeftModel, $RightModel, $conditions);

			$assoc = $LeftModel->getAssociated($RightModel->alias);
			$field = ($assoc['association'] == 'belongsTo') ? $assoc['foreignKey'] : $LeftModel->primaryKey;

			$conditions = [
				"{$LeftModel->alias}.$field IN ($subQuery)"
			];
		}

		if ($notIn) {
			$conditions = [
				"{$LeftModel->alias}.$field NOT IN ($subQuery)"
			];
		}

		$this->where($conditions);
	}

/**
 * Builds connection query of two associated models.
 * 
 * @param Model $Model Main model.
 * @param Model $AssocModel Associated model to Main model.
 * @param array $conditions Additional conditions on Associated model.
 * @return AdvancedQuery Connection model.
 */
	public static function assocSubQuery($Model, $AssocModel, $conditions = []) {
		$assoc = $Model->getAssociated($AssocModel->alias);

		if (empty($assoc)) {
			return false;
		}

		$QueryModel = $AssocModel;
		$field = $assoc['foreignKey'];
		$assocConditions = (!empty($assoc['conditions'])) ? $assoc['conditions'] : [];

		if ($assoc['association'] == 'belongsTo') {
			$field = $AssocModel->primaryKey;
		}
		elseif ($assoc['association'] == 'hasAndBelongsToMany') {
			$subQuery = new AdvancedQuery($AssocModel, 'all', [
				'fields' => ["$AssocModel->alias.$AssocModel->primaryKey"],
				'conditions' => $conditions,
			]);
			$QueryModel = self::getModelInstance($assoc['with']);
			$conditions = [
				"$QueryModel->alias.{$assoc['associationForeignKey']}" => $subQuery
			];
		}

		$query = new AdvancedQuery($QueryModel, 'all', [
			'fields' => ["$QueryModel->alias.$field"],
			'conditions' => array_merge($assocConditions, $conditions),
		]);

		return $query;
	}

/**
 * Set select fields to query options.
 * 
 * @param Array Fields.
 * @return void
 */
	public function select($fields = []) {
		$this->_queryOptions['fields'] = array_merge($this->_queryOptions['fields'], $fields);
	}

/**
 * Set contain to query options.
 * 
 * @param Array Contain.
 * @return void
 */
	public function contain($contain = []) {
		$this->_queryOptions['contain'] = array_merge($this->_queryOptions['contain'], $contain);
	}

/**
 * Set joins to query options.
 * 
 * @param Array Joins.
 * @return void
 */
	public function joins($joins = []) {
		$this->_queryOptions['joins'] = array_merge($this->_queryOptions['joins'], $joins);
	}

/**
 * Set order to query options.
 * 
 * @param Array Order.
 * @return void
 */
	public function order($order = []) {
		$this->_queryOptions['order'] = array_merge($this->_queryOptions['order'], $order);
	}

/**
 * Set group to query options.
 * 
 * @param Array Group.
 * @return void
 */
	public function group($group = []) {
		$this->_queryOptions['group'] = array_merge($this->_queryOptions['group'], $group);
	}

/**
 * Set offset to query options.
 * 
 * @param int Offset.
 * @return void
 */
	public function offset($offset = null) {
		$this->_queryOptions['offset'] = $offset;
	}

/**
 * Set page to query options.
 * 
 * @param int Page.
 * @return void
 */
	public function page($page = null) {
		$this->_queryOptions['page'] = $page;
	}

/**
 * Set limit to query options.
 * 
 * @param int Limit.
 * @return void
 */
	public function limit($limit = null) {
		$this->_queryOptions['limit'] = $limit;
	}

/**
 * Set recursive to query options.
 * 
 * @param int Recursive.
 * @return void
 */
	public function recursive($recursive = null) {
		$this->_queryOptions['recursive'] = $recursive;
	}

/**
 * Execute query and get data.
 * 
 * @param String $finder Finder type.
 * @return array Array of data.
 */
	public function get($finder = null) {
		$this->finder($finder);

		$options = $this->options();
		$finder = $this->finder();
		$options['conditions'] = $this->_processObjects($options['conditions']);

		$runAdvancedQueryResult = (in_array($this->finder(), ['all', 'first'])) ? true : false;

		if ($this->finder() == 'first') {
			$finder = 'all';
			$options['limit'] = 1;
		}

		if ($runAdvancedQueryResult) {
			$contain = $options['contain'];
			$options['contain'] = ($contain !== null) ? [] : null;
		}

		$data = $this->model()->find(
			$finder,
			$options
		);

		if ($runAdvancedQueryResult) {
			$data = $this->_advancedQueryResult($data);
		}

		if ($this->finder() == 'first' && is_array($data) && isset($data[0])) {
			$data = $data[0];
		}

		return $data;
	}

/**
 * Handle advanced query result.
 * 
 * @param array $data
 * @return mixed
 */
	public function _advancedQueryResult($data)
	{
		$Result = new AdvancedQueryResult($data, $this->model());

		if (!empty($this->options()['contain'])) {
			foreach ($this->options()['contain'] as $key => $value) {
				$alias = (is_string($value)) ? $value : $key;
				$options = (is_string($value)) ? [] : $value;

				// we skip non-existent association
				if ($this->model()->{$alias} == null) {
					continue;
				}

				foreach ($options as $optKey => $optValue) {
					$optAlias = (is_string($optValue)) ? $optValue : $optKey;
					$optOptions = (is_string($optValue)) ? [] : $optValue;

					if (!empty($this->model()->{$alias}->{$optAlias})) {
						$options['contain'][$optAlias] = $optOptions;
						unset($options[$optKey]);
					}
				}

				$Result->attach($alias, $options);
			}
		}

		return ($this->options()['raw']) ? $Result->data() : $Result;
	}

/**
 * Get query string. There is no support for contain.
 * 
 * @param String $finder Finder type.
 * @return String Query.
 */
	public function getQuery($finder = null) {
		$this->finder($finder);

		$options = $this->options();
		$options['conditions'] = $this->_processObjects($options['conditions']);

		$Model = $this->model();
		$ds = $Model->getDataSource();

		$baseSettings = [
			'table' => $ds->fullTableName($Model),
			'alias' => $Model->alias,
		];
		$statementSettings = array_merge($baseSettings, $options);

        $query = $ds->buildStatement($statementSettings, $Model);

        return $query;
	}

/**
 * Find and convert all query objects like QueryTemplate and AdvancedQuery to strings to be able to execute the query.
 * 
 * @param Array Data with objects. 
 * @return Array Data with no objects.
 */
	protected function _processObjects($data) {
		if (!is_array($data)) {
			return false;
		}

		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$data[$key] = $this->_processObjects($value);
			}
			elseif ($value instanceof QueryTemplate) {
				$data[$key] = $value->toString();
			}
			elseif ($value instanceof AdvancedQueryBuilder) {
				$data[] = "$key IN ({$value->getQuery()})";
				unset($data[$key]);
			}
			elseif ($value instanceof AdvancedQuery) {
				$data[] = "$key IN ({$value->getQuery()})";
				unset($data[$key]);
			}
		}

		return $data;
	}

/**
 * Try to find subject model in conditions data.
 * 
 * @param Array Conditions. 
 * @return String Model name.
 */
	protected function _findModelInConditions($conditions) {
		$model = null;

		foreach ($conditions as $key => $value) {
			if (is_array($value)) {
				$model = $this->_findModelInConditions($value);
			}
			if (!empty($model)) {
				break;
			}

			$model = $this->_findModelInValue($key);
			if (!empty($model)) {
				break;
			}

			$model = $this->_findModelInValue($value);
			if (!empty($model)) {
				break;
			}
		}

		return $model;
	}

/**
 * Try to find subject model in value.
 * 
 * @param mixed Data. 
 * @return String Model name.
 */
	protected function _findModelInValue($field) {
		$model = null;

		if (is_string($field)) {
			$model = $this->modelSplit($field);
		}
		elseif ($field instanceof QueryTemplate) {
			$params = $field->params();
			if (is_array($params) && !empty($params['field'])) {
				$model = $this->modelSplit($params['field']);
			}
		}

		return $model;
	}

/**
 * Get model part from field name.
 * 
 * @param String field. 
 * @return String Model name.
 */
	public function modelSplit($field) {
		list($model, $field) = pluginSplit($field);
		return $model;
	}

/**
 * Get model instance.
 * 
 * @param String|Model Model name or instance. 
 * @return Model Model instance.
 */
	public static function getModelInstance($model) {
		if (is_string($model)) {
			$model = ClassRegistry::init($model);
		}

		return $model;
	}

}