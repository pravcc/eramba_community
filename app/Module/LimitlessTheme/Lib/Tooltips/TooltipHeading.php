<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipHeading extends TooltipElement
{
	protected $tag = 'h1';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$heading = $this->getStartTag();
		$heading .= $this->content;
		$heading .= $this->getEndTag();

		return $heading;
	}
}