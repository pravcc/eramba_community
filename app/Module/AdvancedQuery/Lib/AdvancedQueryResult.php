<?php
App::uses('ClassRegistry', 'Utility');
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('Hash', 'Utility');

/**
 * AdvancedQuery result set.
 */
class AdvancedQueryResult
{
	/**
	 * Related model instance.
	 * 
	 * @var Model
	 */
	protected $_Model = null;

	/**
	 * Data.
	 * 
	 * @var mixed
	 */
	protected $_data = [];

	/**
	 * Related results.
	 * 
	 * @var array
	 */
	protected $_relatedResults = [
	];

	public function __construct($data, $Model)
	{
		$this->_Model = $Model;
		$this->_data = $data;
	}

	/**
	 * Get result set model.
	 * 
	 * @return Model
	 */
	public function model()
	{
		return $this->_Model;
	}

	/**
	 * Get data.
	 *
	 * @param array $conditions
	 * @param boolean $deep Deep related data.
	 * @return array Data.
	 */
	public function data($conditions = [], $deep = true)
	{
		$data = [];

		foreach ($this->filterData($conditions) as $item) {
			foreach ($this->_relatedResults as $alias => $Result) {
				$assocConditions = $this->_filterAssocConditions($item, $alias);
				$assocData = $Result->data($assocConditions);

				$associated = $this->model()->getAssociated($alias);

				$finalAssocData = [];

				if ($associated['association'] == 'belongsTo') {
					$finalAssocData = Hash::get($assocData, "0.{$alias}");
				}
				elseif ($associated['association'] == 'hasOne') {
					$finalAssocData = Hash::extract($assocData, "0.{$alias}");
				}
				elseif ($associated['association'] == 'hasMany') {
					$finalAssocData = Hash::extract($assocData, "{n}.{$alias}");
				}
				elseif ($associated['association'] == 'hasAndBelongsToMany') {
					$With = ClassRegistry::init($associated['with']);

					foreach ($assocData as $assocDataKey => $assocDataItem) {
						$assocData[$assocDataKey][$alias][$With->alias] = $assocDataItem[$With->alias];
					}

					$finalAssocData = Hash::extract($assocData, "{n}.{$alias}");
				}

				if (empty($conditions)) {
					$item[$alias] = $finalAssocData;
				}
				else {
					$item[$this->model()->alias][$alias] = $finalAssocData;
				}
			}

			$data[] = $item;
		}

		return $data;
	}

	/**
	 * Get related data conditions.
	 * 
	 * @param array $item Item data.
	 * @param string $alias Association name.
	 * @return array Conditions.
	 */
	protected function _filterAssocConditions($item, $alias)
	{
		$associated = $this->model()->getAssociated($alias);

		$conditions = [];

		if ($associated['association'] == 'belongsTo') {
			$conditions = [
				"{$alias}.id" => Hash::get($item, "{$this->model()->alias}.{$associated['foreignKey']}")
			];
		}
		elseif ($associated['association'] == 'hasOne') {
			$conditions = array_merge([
				"{$alias}.{$associated['foreignKey']}" => Hash::get($item, "{$this->model()->alias}.id")
			], (!empty($associated['conditions'])) ? $associated['conditions'] : []);
		}
		elseif ($associated['association'] == 'hasMany') {
			$conditions = array_merge([
				"{$alias}.{$associated['foreignKey']}" => Hash::get($item, "{$this->model()->alias}.id")
			], (!empty($associated['conditions'])) ? $associated['conditions'] : []);
		}
		elseif ($associated['association'] == 'hasAndBelongsToMany') {
			$With = ClassRegistry::init($associated['with']);

			$conditions = array_merge([
				"{$With->alias}.{$associated['foreignKey']}" => Hash::get($item, "{$this->model()->alias}.id")
			], (!empty($associated['conditions'])) ? $associated['conditions'] : []);
		}

		return $conditions;
	}

	/**
	 * Filter result set data by conditions.
	 * 
	 * @param array $conditions
	 * @return array Filtered data.
	 */
	public function filterData($conditions = [])
	{
		$data = [];

		foreach ($this->_data as $item) {
			if ($this->_matchConditions($item, $conditions)) {
				$data[] = $item;
			}
		}

		return $data;
	}

	/**
	 * Check if data matches conditions.
	 * 
	 * @param array $data
	 * @param array $conditions
	 * @return boolean
	 */
	protected function _matchConditions($data, $conditions = [])
	{
		foreach ($conditions as $field => $value) {
			$dataValue = Hash::get($data, $field);

			if (is_array($dataValue)) {
				return false;
			}

			if (is_array($value) && !in_array($dataValue, $value)) {
				return false;
			}

			if (!is_array($value) && $dataValue != $value) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Attach related result set.
	 * @param string $assoc Association name.
	 * @param array $options Find options.
	 * @return void
	 */
	public function attach($assoc, $options = [])
	{
		$assocOptions = $this->_getAttachFindOptions($assoc);

		//ensure order is array
		if (!empty($options['order'])) {
			$options['order'] = (array) $options['order'];
		}

		$options = Hash::merge($options, $assocOptions);

		if (empty($options['contain'])) {
			$options['contain'] = [];
		}

		$Query = new AdvancedQuery($this->model()->{$assoc}, 'all', $options);

		$this->_relatedResults[$assoc] = $Query->get();
	}

	/**
	 * Find options for related find.
	 * 
	 * @param string $alias Association name.
	 * @return array Find options.
	 */
	protected function _getAttachFindOptions($alias)
	{
		$options = [
			'raw' => false
		];

		$Model = $this->model()->{$alias};
		$associated = $this->model()->getAssociated($alias);

		if ($associated['association'] == 'belongsTo') {
			$ids = Hash::extract($this->_data, "{n}.{$this->model()->alias}.{$associated['foreignKey']}");

			$options['conditions'] = [
				"{$Model->alias}.id" => $ids
			];
		}
		elseif ($associated['association'] == 'hasOne') {
			$ids = Hash::extract($this->_data, "{n}.{$this->model()->alias}.id");

			$options['conditions'] = array_merge([
				"{$Model->alias}.{$associated['foreignKey']}" => $ids
			], (!empty($associated['conditions'])) ? $associated['conditions'] : []);
			$options['group'] = [
				"{$Model->alias}.{$associated['foreignKey']}"
			];
		}
		elseif ($associated['association'] == 'hasMany') {
			$ids = Hash::extract($this->_data, "{n}.{$this->model()->alias}.id");

			$options['conditions'] = array_merge([
				"{$Model->alias}.{$associated['foreignKey']}" => $ids
			], (!empty($associated['conditions'])) ? $associated['conditions'] : []);
		}
		elseif ($associated['association'] == 'hasAndBelongsToMany') {
			$With = ClassRegistry::init($associated['with']);

			$ids = Hash::extract($this->_data, "{n}.{$this->model()->alias}.id");

			$options['conditions'] = array_merge([
				"{$With->alias}.{$associated['foreignKey']}" => $ids
			], (!empty($associated['conditions'])) ? $associated['conditions'] : []);

			$options['joins'][] = [
				'table' => $With->useTable,
				'alias' => $With->alias,
				'type' => 'INNER',
				'conditions' => [
					"{$With->alias}.{$associated['associationForeignKey']} = {$Model->alias}.id" 
				]
			];

			$options['fields'] = ['*'];
		}

		if (!empty($associated['order'])) {
			$options['order'] = (array) $associated['order'];
		}

		if (!empty($associated['limit'])) {
			$options['limit'] = (array) $associated['limit'];
		}

		return $options;
	}
}