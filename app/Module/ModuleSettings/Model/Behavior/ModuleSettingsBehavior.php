<?php
App::uses('ModelBehavior', 'Model');

/**
 * FieldDataBehavior
 */
class ModuleSettingsBehavior extends ModelBehavior {

	protected $_runtime = [];

	/**
	 * Default config
	 * 
	 * enabled 			Enable/disable this behavior.
	 * modelColumn 		Column name for the {module}_settings table which stores $Model->alias.
	 * statusColumn 	Column name for the {module}_settings table that holds status value (tinyint)
	 * 					about the module, if it is enabled or disabled (or other custom statuses).
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'enabled' => true,
		'modelColumn' => 'model',
		'statusColumn' => 'status'
	);

	public $settings = [];

	/**
	 * Setup
	 *
	 * @param Model $Model
	 * @param array $settings
	 * @return void
	 */
	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
		}

		if (Configure::read('debug')) {

		}
	}

/**
 * Runs before a find() operation
 *
 * @param Model $Model Model using the behavior
 * @param array $query Query parameters as set by cake
 * @return array
 */
	public function beforeFind(Model $Model, $query) {
		if (!isset($query['conditions'])) {
			$query['conditions'] = [];
		}

		// we alter the conditions to hide debug information (rows) only in case debug is disabled
		if (!Configure::read('debug')) {
			$query['conditions'] = am(
				$this->getModuleSettingsConditions($Model),
				$query['conditions']
			);
		}

		return $query;
	}

	// get additional conditions for find query that includes handler for debug.
	public function getModuleSettingsConditions(Model $Model) {
		return [
			$Model->escapeField('model') . ' !=' => $this->getDebugModelAliases($Model)
		];
	}

	// model aliases used for debugging
	public function getDebugModelAliases(Model $Model) {
		return ['SectionItem'];
	}

}
