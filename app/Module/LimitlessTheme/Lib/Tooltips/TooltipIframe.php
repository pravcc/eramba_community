<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipIframe extends TooltipElement
{
	protected $tag = 'iframe';

	protected $sourceUrl = '';
	protected $sourceType = '';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$iframe = $this->getStartTag();
		$iframe .= $this->getEndTag();

		return $iframe;
	}
}