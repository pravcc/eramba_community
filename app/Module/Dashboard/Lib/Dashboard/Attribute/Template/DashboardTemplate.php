<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CakeObject', 'Core');

class DashboardTemplate extends CakeObject {

	/**
	 * Template title.
	 * 
	 * @var null|string
	 */
	public $template = null;

	/**
	 * Template configuration.
	 * 
	 * @var null|array
	 */
	protected $_config = null;

	/**
	 * Constructor for a dashboard attribute template which depends on AdvancedFilters module.
	 * 
	 * @param string $template   Template name.
	 * @param array  $parameters Parameters for the template.
	 */
	public function __construct($template, $parameters) {
		$this->template = $template;
		$this->_config = $this->_normalize($parameters);
	}

	// get the title but processed with sprintf() accepting any arguments as values in the title.
	public function getTitle() {
		$args = func_get_args();
		array_unshift($args, $this->get('title'));

		return call_user_func_array('sprintf', $args);
	}

	public function softDelete() {
		return $this->get('softDelete');
	}

	/**
	 * Read any value from this template's config.
	 * 
	 * @param  string $param Configuration key for array from which to read and return the value.
	 * @return mixed
	 */
	public function get($param) {
		return Hash::get($this->_config, $param);
	}

	/**
	 * Helper method normalizes template configuration array and returns final config.
	 * 
	 * @param  array $parameters  Array with the attribute template config.
	 * @return array              Normalized configuration.
	 */
	protected function _normalize($parameters) {
		return Hash::merge([
			'title' => null,
			'softDelete' => true
		], $parameters);
	}

}