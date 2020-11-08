<?php
/**
 * AbstractConfiguration base class.
 */
abstract class AbstractConfiguration
{
	/**
	 * Configuration array. Configs are stored in associative array formated like [configName => configValue].
	 * 
	 * @var array
	 */
	protected $_configuration = [];

	/**
	 * Set config value under given name. Only existing configs are accessible.
	 * 
	 * @param  string $name Config name.
	 * @param  mixed $value Config value.
	 * @return bool False if config with given name doesnt exist othervise true.
	 */
	protected function _setConfig(string $name, $value)
	{
		$allowedConfigs = array_keys($this->_configuration);

		if (!in_array($name, $allowedConfigs)) {
			return false;
		}

		$this->_configuration[$name] = $value;

		return true;
	}

	/**
	 * Get the value of config by name.
	 * 
	 * @param  string $name Config name.
	 * @return mixed Value of config, null if config doesnt exist.
	 */
	protected function _getConfig(string $name)
	{
		return isset($this->_configuration[$name]) ? $this->_configuration[$name] : null;
	}

	/**
	 * Set and get config value. If given value is null only get is executed.
	 * 
	 * @param  string $name Config name.
	 * @param  mixed $value Config value.
	 * @return mixed Config value.
	 */
	public function _config(string $name, $value = null)
	{
		if ($value !== null) {
			$this->_setConfig($name, $value);
		}

		return $this->_getConfig($name);
	}

	/**
	 * Bulk config set.
	 * 
	 * @param  array $configs Associative array of configs [name => value, name2 => value2 ...].
	 * @return void
	 */
	public function config(array $configs)
	{
		foreach ($configs as $key => $value) {
			$this->_setConfig($key, $value);
		}
	}
}