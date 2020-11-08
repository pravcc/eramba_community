<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');
App::uses('AuthComponent', 'Controller/Component');

class AdvancedFiltersView extends CrudView {

	/**
	 * Instance of AdvancedFilter model.
	 * 
	 * @var AdvancedFilter
	 */
	public $Instance;

	/**
	 * Count of saved filter.
	 * 
	 * @var integer
	 */
	protected $_count;

	/**
	 * Saved filters.
	 * 
	 * @var array
	 */
	protected $_savedFilters = [];

	/**
	 * Initialize callback logic that sets the trash counter.
	 * 
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();

		$this->Instance = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		$this->_setCount();
		$this->_setSavedFilters();
	}

	/**
	 * Set saved filters for this View class instance.
	 * 
	 * @return void
	 */
	public function _setSavedFilters()
	{
		$model = $this->_model()->alias;

		$cacheKey = 'toolbar_data_' . $this->getSubject()->model->alias . '_user_' . AuthComponent::user('id');

		if (($filters = Cache::read($cacheKey, 'advanced_filters_settings')) === false) {
			$filters = $this->Instance->find('list', array(
				'conditions' => $this->_getConditions($model),
				'order' => [
					'AdvancedFilter.name' => 'ASC'
				],
				'recursive' => -1
			));

			Cache::write($cacheKey, $filters, 'advanced_filters_settings');
		}

		$this->_savedFilters[$model] = $filters;
	}

	/**
	 * Get saved filters.
	 * 
	 * @return array
	 */
	public function getSavedFilters($model)
	{
		return $this->_savedFilters[$model];
	}

	/**
	 * Generic conditions for getting saved filters.
	 * 
	 * @return array Conditions array.
	 */
	protected function _getConditions($model)
	{
		$controller = $this->_controller();

		return [
			'AdvancedFilter.model' => $model,
			// 'AdvancedFilter.user_id' => $controller->logged['id'],
			'OR' => [
				[
					'AdvancedFilter.private' => ADVANCED_FILTER_NOT_PRIVATE,
				],
				[
					'AdvancedFilter.private' => ADVANCED_FILTER_PRIVATE,
					'AdvancedFilter.user_id' => $controller->logged['id'],
				],
			]
		];
	}

	/**
	 * Find and set count of deleted objects.
	 */
	protected function _setCount()
	{
		$model = $this->_model()->alias;

		$cacheKey = 'toolbar_count_' . $this->getSubject()->model->alias . '_user_' . AuthComponent::user('id');

		if (($count = Cache::read($cacheKey, 'advanced_filters_settings')) === false) {
			$count = $this->Instance->find('count', array(
				'conditions' => $this->_getConditions($model),
				'recursive' => -1
			));

			Cache::write($cacheKey, $count, 'advanced_filters_settings');
		}
		
		$this->_count[$model] = $count;
	}

	public function hasItems($model)
	{
		return $this->_count[$model] > 0;
	}

	/**
	 * Get the count of deleted objects.
	 * 
	 * @return int
	 */
	public function getCount($model)
	{
		return $this->_count[$model];
	}

}
