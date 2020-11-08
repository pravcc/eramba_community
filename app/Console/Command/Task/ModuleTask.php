<?php
App::uses('PluginTask', 'Console/Command/Task');

/**
 * App's form of separated functionality as Plugins that are having extra properties but are extended with original functionality.
 *
 * @package       App.Console.Command.Task
 */
class ModuleTask extends PluginTask {

/**
 * path to plugins directory
 *
 * @var array
 */
	public $path = null;

/**
 * Path to the bootstrap file. Changed in tests.
 *
 * @var string
 */
	public $bootstrap = null;

/**
 * initialize
 *
 * @return void
 */
	public function initialize() {
		$paths = App::path('plugins');

		// custom modules path
		$this->path = end($paths);
		$this->bootstrap = APP . 'Config' . DS . 'bootstrap.php';
	}

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		if (isset($this->args[0])) {
			$plugin = Inflector::camelize($this->args[0]);
			$pluginPath = $this->_pluginPath($plugin);
			if (is_dir($pluginPath)) {
				$this->out(__d('cake_console', 'App Module: %s already exists, no action taken', $plugin));
				$this->out(__d('cake_console', 'Path: %s', $pluginPath));
				return false;
			}
			$this->_interactive($plugin);
		} else {
			return $this->_interactive();
		}
	}

/**
 * Interactive interface
 *
 * @param string $plugin The plugin name.
 * @return void
 */
	protected function _interactive($plugin = null) {
		while ($plugin === null) {
			$plugin = $this->in(__d('cake_console', 'Enter the name of the App Module in CamelCase format'));
		}

		if (!$this->bake($plugin)) {
			$this->error(__d('cake_console', "An error occurred trying to bake: %s in %s", $plugin, $this->path . $plugin));
		}
	}

	public function findPath($pathOptions) {
		$paths = App::path('plugins');

		// custom modules path
		$this->path = end($paths);
	}

/**
 * Bake the plugin, create directories and files and extras for app modules
 *
 * @param string $plugin Name of the plugin in CamelCased format
 * @return bool
 */
	public function bake($plugin) {
		// leave plugin setup like it should, and add some additional files to be used as convention code the same way
		$ret = parent::bake($plugin);

		// we add module's own class available application-wide having general logic and code for the module, named exactly same as the plugin
		$libFileName = $plugin . 'Module.php';
		$out = "<?php\n";
		$out .= "App::uses('ModuleBase', 'Lib');\n";
		$out .= "class {$plugin}Module extends ModuleBase {\n\n";
		$out .= "}\n";
		$ret &= $this->createFile($this->path . $plugin . DS . 'Lib' . DS . $libFileName, $out);

		// bootstrap loads the module's own class to runtime that makes it available anywhere in case bootstrap was loaded as well for the module
		$bootstrapFile = 'bootstrap.php';
		$out = "<?php\n";
		$out .= "App::uses('{$plugin}Module', '{$plugin}.Lib');";
		$ret &= $this->createFile($this->path . $plugin . DS . 'Config' . DS . $bootstrapFile, $out);

		// set up default versioning into VERSION file to be used for the module the same way as we have for App itself
		$moduleVersionFile = 'VERSION';
		$out = "1.0.0-dev";
		$ret &= $this->createFile($this->path . $plugin . DS . $moduleVersionFile, $out);

		return $ret;
	}

/**
 * Update the app's bootstrap.php file.
 *
 * @param string $plugin Name of plugin
 * @return void
 */
	protected function _modifyBootstrap($plugin) {
		$bootstrap = new File($this->bootstrap, false);
		$contents = $bootstrap->read();
		if (!preg_match("@\n\s*AppModule::loadAll@", $contents)) {
			$bootstrap->append("\nCakePlugin::load('$plugin', array('bootstrap' => false, 'routes' => false));\n");
			$this->out('');
			$this->out(__d('cake_dev', '%s modified', $this->bootstrap));
		}
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(
			__d('cake_console',	'Create App Module which is the same functionality based on plugins with some extras.')
		)->addArgument('name', array(
			'help' => __d('cake_console', 'CamelCased name of the App Module to create.'),
			'index' => 0
		));

		return $parser;
	}

}
