<?php
App::uses('ModelBehavior', 'Model');

/**
 * DashboardAttributeBehavior
 */
class DashboardAttributeBehavior extends ModelBehavior {

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'enabled' => true
	);

	public $settings = [];

	/**
	 * Setup
	 *
	 * @param Model $Model
	 * @param array $settings
	 * @throws RuntimeException
	 * @return void
	 */
	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
		}
	}

	

}
