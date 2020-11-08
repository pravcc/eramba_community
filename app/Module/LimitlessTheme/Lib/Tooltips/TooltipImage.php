<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipImage extends TooltipElement
{
	protected $tag = 'img';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$image = $this->getStartTag();

		return $image;
	}
}