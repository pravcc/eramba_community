<?php
App::uses('StringTemplate', 'Lib');

/**
 * Base Macro class.
 */
class Macro
{
/**
 * Macro replacable alias without special wrapping charactrs.
 * 
 * @var string
 */
	protected $_alias = null;

/**
 * Macro name/label.
 * 
 * @var string
 */
	protected $_label = null;

/**
 * Value or callback to get macro value.
 * 
 * @var mixed
 */
	protected $_value = null;

/**
 * Subject of macro, we use this subject as a paramater for value callback.
 * 
 * @var mixed
 */
	protected $_subject = null;

/**
 * Wrapping special chars template.
 * 
 * @var string
 */
	protected $_template = '%{macro}%';

/**
 * Construnct.
 * 
 * @param string $alias
 * @param string $label
 * @param mixed $subject
 * @param mixed $value
 */
	public function __construct($alias, $label, $subject, $value) {
		$this->_alias = $alias;
		$this->_label = $label;
		$this->_subject = $subject;
		$this->_value = $value;
	}

/**
 * Full macro alias with wrapping special chars.
 * 
 * @return string Full macro replacable alias.
 */
	public function macro() {
		$template = new StringTemplate($this->_template, ['macro' => $this->alias()]);
		return $template->toString();
	}

/**
 *  Alias without wrapping special chars.
 * 
 * @return string Alias.
 */
	public function alias() {
		return $this->_alias;
	}

/**
 * Get label of macro.
 * 
 * @return string Label.
 */
	public function label() {
		return $this->_label;
	}

/**
 * Get subject of macro.
 * 
 * @return mixed subject.
 */
	public function subject() {
		return $this->_subject;
	}

/**
 * Find macro in text and replace it by value.
 *
 * @param string $text Text with possible macros.
 * @param string $data Subject item data.
 * @return string Text with replaced macros by values.
 */
	public function apply(&$text, $data = null) {
		if (strpos($text, $this->macro()) !== false) {
			return str_replace($this->macro(), $this->getValue($data), $text);
		}

		return $text;
	}

/**
 * Get macro value.
 *
 * @param string $data Subject item data.
 * @return string Macro value.
 */
	public function getValue($data) {
		if (is_callable($this->_value)) {
			return call_user_func($this->_value, $data, $this->_subject);
		}
		
		return $this->_value;
	}

}