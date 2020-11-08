<?php
/**
 * Base String Template.
 */
class StringTemplate
{

/**
 * Template string. Define parameters by "{param}".
 * 
 * @var string
 */
	protected $_template = '';

/**
 * Template parameters.
 * 
 * @var array
 */
	protected $_params = '';

/**
 * Construct.
 * 
 * @param string $template Template.
 * @param array $params Parameters.
 */
	public function __construct($template, $params = []) {
		$this->_template = $template;
		$this->_params = $params;
	}

/**
 * Convert instance to string.
 * 
 * @return string
 */
	public function __toString() {
		return static::process($this->_template, $this->_params);
	}

/**
 * Convert instance to string.
 * 
 * @return string
 */
	public function toString() {
		return static::process($this->_template, $this->_params);
	}

/**
 * Place parameters to template string.
 * 
 * @return string Template string with replaced parameters.
 */
	public static function process($template, $params = []) {
		foreach ($params as $name => $value) {
			$template = str_replace('{' . $name . '}', $value, $template);
		}

		return $template;
	}
}