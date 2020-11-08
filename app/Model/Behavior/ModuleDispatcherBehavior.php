<?php
App::uses('ModelBehavior', 'Model');
App::uses('AppModule', 'Lib');

/**
 * 
 */
class ModuleDispatcherBehavior extends ModelBehavior
{

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'behaviors' => null
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
	public function setup(Model $Model, $settings = array())
	{
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
		}


		$this->_initialize($Model);
	}

	protected function _initialize(Model $Model)
	{
		$behaviors = $this->settings[$Model->alias]['behaviors'];
		if (!is_array($behaviors)) {
			$behaviors = (array) $behaviors;
		}

		$behaviors = Hash::normalize($behaviors);

		foreach ($behaviors as $name => $config) {
			list($plugin, $className) = pluginSplit($name);

			if (in_array($plugin, AppModule::getEnabledModules())) {
				if (!is_array($config)) {
					$config = (array) $config;
				}
				
				$Model->Behaviors->load($name, $config);
			}
		}
	}

}