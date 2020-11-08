<?php
App::uses('LimitlessThemeException', 'Module/LimitlessTheme/Error');
App::uses('TablesTrait', 'Module/LimitlessTheme/Lib/Tables/Trait');
App::uses('Table', 'Module/LimitlessTheme/Lib/Tables');
App::uses('TableBlock', 'Module/LimitlessTheme/Lib/Tables');
App::uses('TableRow', 'Module/LimitlessTheme/Lib/Tables');
App::uses('TableColumn', 'Module/LimitlessTheme/Lib/Tables');

class TableElement
{
	use TablesTrait;

	/**
	 * Type of the block
	 * Options:
	 * 	- table
	 * 	- table_header
	 * 	- table_body
	 * 	- table_footer
	 */
	protected $blockType = 'table';

	protected $name = '';
	protected $class = '';
	protected $style = '';
	protected $tag = '';
	protected $attributes = [];
	protected $content = '';

	public function __construct(string $name = '')
	{
		$this->setName($name);
	}

	public function getStartTag(bool $setAttributes = true)
	{
		$attributes = ' id="' . $this->name . '" class="' . $this->class . '" style="' . $this->style . '"';
		foreach ($this->attributes as $attr => $val) {
			$attributes .= ' ' . $attr . '="' . $val . '"';
		}
		return '<' . $this->tag . ' ' . $attributes . '>';
	}

	public function getEndTag()
	{
		return '</' . $this->tag . '>';
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function setClass(string $class)
	{
		$this->class = $class;
	}

	public function setStyle(string $style)
	{
		$this->style = $style;
	}

	public function setTag(string $tag)
	{
		$this->tag = $tag;
	}

	public function addAttribute($name, $val)
	{
		$this->attributes[$name] = $val;
	}

	public function removeAttribute($name)
	{
		unset($this->attributes[$name]);
	}

	public function setContent(string $content)
	{
		$this->content = $content;
	}

	public function setBlockType(string $type)
	{
		$this->isBlockTypeAllowed($type);

		$this->blockType = $type;
	}

	protected function isBlockTypeAllowed(string $type)
	{
		if (!in_array($type, ['table', 'table_header', 'table_body', 'table_footer'])) {
			throw new LimitlessThemeException(__('The type of the table block (%s) you\'re trying to use doesn\'t exists', $type));
		}
	}

	/**
	 * Apply options to object
	 * Options example:
	 * 	[
	 * 		classParamName => classParamValue
	 * 	]
	 * An object has to have setter function defined for each given classParamName (to process classParamName from 
	 * options array the target object has to have defined setClassParamName method)
	 * 	
	 * @param  array $options Options with classParamName => classParamValue(s)
	 */
	public function applyOptions($options)
	{
		foreach ($options as $method => $params) {
			$fullMethod = 'set' . ucfirst($method);
			if (method_exists($this, $fullMethod)) {
				call_user_func_array([$this, $fullMethod], is_array($params) ? $params : [$params]);
			}
		}
	}
}