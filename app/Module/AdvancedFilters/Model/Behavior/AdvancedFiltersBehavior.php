<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('FilterField', 'AdvancedFilters.Lib');
App::uses('FilterConfigurationBuilder', 'AdvancedFilters.Lib/Configuration');

/**
 * AdvancedFiltersBehavior
 */
class AdvancedFiltersBehavior extends ModelBehavior {

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'enabled' => true,
		'config' => []
	);

	public $settings = [];

	protected $_runtime = [];

	/**
	 * Setup
	 *
	 * @param Model $Model
	 * @param array $settings
	 * @return void
	 */
	public function setup(Model $Model, $settings = array())
	{
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
			$this->_runtime[$Model->alias] = [];
		}

		$this->_parseConfig($Model);
	}

	protected function _parseConfig(Model $Model)
	{
		// boilerplate for config to develop dependencies
		$configTemplate = Hash::normalize(array_keys(array_filter($Model->filterArgs)));

		// store array of possible field names only for further use
		$this->settings[$Model->alias]['config'] = $configTemplate;
	}

	public function filterField(Model $Model, $field)
	{
		$cond = array_key_exists($field, $this->settings[$Model->alias]['config']);
		$cond = $cond && !isset($this->_runtime[$Model->alias][$field]);
		if ($cond) {
			$this->_runtime[$Model->alias][$field] = new FilterField($Model, $field, []);
		}

		if (!isset($this->_runtime[$Model->alias][$field])) {
			return false;
		}

		return $this->_runtime[$Model->alias][$field];
	}

	/**
	 * Returns builder instance for advanced filters config.
	 * 
	 * @param  Model $Model
	 * @return FilterConfigurationBuilder
	 */
	public function createAdvancedFilterConfig(Model $Model)
	{
		return new FilterConfigurationBuilder($Model);
	}

	public function buildAdvancedFilterArgs(Model $Model)
	{
		if (!empty($Model->advancedFilter) || !method_exists($Model, 'getAdvancedFilterConfig')) {
			return;
		}

		$Model->advancedFilter = call_user_func([$Model, 'getAdvancedFilterConfig']);

		$Model->initAdvancedFilter();
	}
}
