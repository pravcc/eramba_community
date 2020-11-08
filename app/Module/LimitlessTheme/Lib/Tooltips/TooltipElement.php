<?php
App::uses('LimitlessThemeException', 'Module/LimitlessTheme/Error');
App::uses('TooltipsTrait', 'Module/LimitlessTheme/Lib/Tooltips/Trait');
App::uses('Tooltip', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipHeader', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipBody', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipFooter', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipRow', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipColumn', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipHeading', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipText', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipImage', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipVideo', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipIframe', 'Module/LimitlessTheme/Lib/Tooltips');
App::uses('TooltipButton', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipElement
{
	use TooltipsTrait;

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

	public function getClass()
	{
		return $this->class;
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