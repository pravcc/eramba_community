<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipText extends TooltipElement
{
	protected $tag = 'p';
	
	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$text = $this->getStartTag();
		$text .= $this->content;
		$text .= $this->getEndTag();

		return $text;
	}
}