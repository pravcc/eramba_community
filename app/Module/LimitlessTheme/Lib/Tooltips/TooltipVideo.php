<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipVideo extends TooltipElement
{
	protected $tag = 'video';

	protected $sourceUrl = '';
	protected $sourceType = '';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$video = '<link href="/css/video-js.css" rel="stylesheet" type="text/css">';
		$video .= $this->getStartTag();
		$video .= '<source src="' . $this->sourceUrl . '" type="' . $this->sourceType . '" />';
		$video .= __('Your browser does not support videos');
		$video .= $this->getEndTag();

		// Add video.js
		$video .= '<script src="/js/video.js"></script>';

		return $video;
	}

	public function setSourceUrl($url)
	{
		$this->sourceUrl = $url;
	}

	public function setSourceType($type)
	{
		$this->sourceType = $type;
	}
}