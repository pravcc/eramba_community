<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipButton extends TooltipElement
{
	protected $tag = 'button';
	protected $class = 'btn btn-default';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$button = $this->getStartTag();
		$button .= $this->content;
		$button .= $this->getEndTag();

		return $button;
	}
}