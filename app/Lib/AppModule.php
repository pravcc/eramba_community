<?php
App::uses('CakePlugin', 'Core');

/**
 * Manage and load app's Modules.
 */
class AppModule {
	/**
	 * Holds module library instances.
	 * 
	 * @var array
	 */
	protected static $_instances = [];

	/**
	 * List of loaded modules in the app.
	 * 
	 * @var array
	 */
	protected static $_modules = array();

	/**
	 * Get the module library instance.
	 * 
	 * @param  string $name Short name of the module (without the -Module suffix)
	 * @return object         Instance.
	 */
	public static function instance($name) {
		if (substr($name, -6, 6) == 'Module') {
			$name = substr($name, 0, strlen($name)-6);
		}

		$className = $name . 'Module';
		App::uses($className, $name . '.Lib');

		if (!class_exists($className)) {
			trigger_error(sprintf('Module library %s doesnt exist.', $className));
			return false;
		}

		if (!isset(static::$_instances[$name]) || !static::$_instances[$name] instanceof ModuleBase) {
			static::$_instances[$name] = new $className();
		}

		return static::$_instances[$name];
	}

	/**
	 * Path to the Module folder, registered in the app as another Plugin folder. 
	 * 
	 * @return string
	 */
	public static function rootPath() {
		return APP . 'Module' . DS;
	}

	/**
	 * List all Modules that are available to the app.
	 * 
	 * @param  boolean $cache Cache results.
	 * @return array          List of names.
	 */
	public static function getAllModules($cache = false) {
		return App::objects('plugin', self::rootPath(), $cache);
	}

	/**
	 * List of enabled Modules in the app.
	 *  
	 * @return array Enabled modules.
	 */
	public static function getEnabledModules() {
		return static::loaded();
	}

	/**
	 * Returns true if the module $module is already loaded
	 * If module is null, it will return a list of all loaded modules
	 *
	 * @param string $module module name to check.
	 * @return mixed boolean true if $module is already loaded.
	 * If $module is null, returns a list of plugins that have been loaded
	 */
	public static function loaded($module = null)
	{
		if ($module) {
			return in_array($module, static::$_modules);
		}

		return static::$_modules;
	}

	/**
	 * Load a single module.
	 * 
	 * @param  string $module Module name.
	 */
	public function load($module)
	{
		if (!in_array($module, static::$_modules)) {
			CakePlugin::load($module, array(
				'ignoreMissing' => true,
				'bootstrap' => true,
				'routes' => true
			));
		
			static::$_modules[] = $module;
		}
	}

	/**
	 * Unload a module or all of them.
	 * 
	 * @param  string $module Module name.
	 */
	public function unload($module)
	{
		if ($module === null) {
			static::$_modules = [];
		} else {
			$key = array_search($module, static::$_modules);
			unset(static::$_modules[$key]);
		}

		CakePlugin::unload($module);
	}

	/**
	 * Autoloader for all Modules available in the app.
	 *
	 * @param array $skip Array of modules that should not be loaded.
	 */
	public static function loadAll($skip = []) {
		$modules = static::getAllModules();
		$modules = array_diff($modules, $skip);

		foreach ($modules as $m) {
			self::load($m);
		}
	}

}