<?php
App::uses('Inflector', 'Utility');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('AppModule', 'Lib');

// Base class for all modules in the app.
abstract class ModuleBase {
	/**
	 * Readable name for the module.
	 * 
	 * @var string
	 */
	public $name = null;

	/**
	 * Alias of a module, by default in a form without 'Module' suffix.
	 * 
	 * @var string
	 */
	public $alias = null;

	/**
	 * Whitelist of section's model aliases where to enable this module.
	 * 
	 * @var array
	 */
	protected $_whitelist = [];

	public $toolbar = false;
	public $action = false;

	public function __construct() {
		$fullClass = get_class($this); // MyFeatureModule
		$class = substr($fullClass, 0, -6); // MyFeature

		if ($this->name === null) {
			$name = Inflector::underscore($class); // my_feature
			$this->name = Inflector::humanize($name); // My Feature
		}

		if ($this->alias === null) {
			$this->alias = $class;  // MyFeature
		}
	}

	/**
	 * Helper static method to get name of a module.
	 * 
	 * @return string         Name.
	 */
	public static function name() {
		return AppModule::instance(get_called_class())->getName();
	}

	/**
	 * Helper static method to get alias of a module.
	 * 
	 * @return string         Alias.
	 */
	public static function alias() {
		return AppModule::instance(get_called_class())->getAlias();
	}

	/**
	 * Name for the module that can be shown to the end user.
	 * 
	 * @return string Readable name of the module.
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Alias for devs.
	 * 
	 * @return string Alias.
	 */
	public function getAlias() {
		return $this->alias;
	}

	/**
	 * Define the url for the access to section's configuration.
	 * 
	 * @param  string $model Model alias.
	 * @return mixed         False to disable this feature or array with URL compatible with Router::url()
	 */
	public function getSectionUrl($model) {
		return false;
	}

	/**
	 * Define the url for the access to section item's configuration.
	 * 
	 * @param  string $model 	  Model alias.
	 * @param  string $foreignKey Foreign key to the item.
	 * @return mixed         	  False to disable this feature or array with URL compatible with Router::url()
	 */
	public function getItemUrl($model, $foreignKey) {
		return false;
	}

	/**
	 * Get the module-enabled section list.
	 * 
	 * @return array List of model aliases.
	 */
	public function whitelist() {
		return $this->_whitelist;
	}

	/**
	 * Add a module alias to the whitelist of enabled sections.
	 * 
	 * @return array List of model aliases.
	 */
	public function addToWhitelist($name) {
		list($plugin, $name) = pluginSplit($name, true);

		App::uses($name, $plugin . 'Model');
		if (!class_exists($name)) {
			trigger_error(sprintf('Model name %s does not exist.', $name));
			return false;
		}

		$this->_whitelist[] = $plugin . $name;

		return $this->_whitelist;
	}

	/**
	 * Email class implementation for modules.
	 * 
	 * @param  array  $options Options.
	 * @return ErambaCakeEmail
	 */
	public function email($options = []) {
		$options = am([
			'layout' => 'default_new',
			'to' => null,
			'subject' => null,
			'template' => null,
			'viewVars' => []
		], $options);

		extract($options);
		if (in_array(null, [$to, $subject, $template])) {
			trigger_error(__('Email created through module class is provided with incomplete configuration.'));
			return false;
		}

		$moduleName = self::name();

		// classify the subject by module name
		$subject = sprintf('%s: %s', $moduleName, $subject);

		if (strpos($template, '.') === false) {
			$template = $moduleName . '.' . $template;
		}

		$email = new ErambaCakeEmail();
		$email->layout($layout);
		$email->to($to);
		$email->subject($subject);
		$email->template($template);
		$email->viewVars($viewVars);

		return $email;
	}
}