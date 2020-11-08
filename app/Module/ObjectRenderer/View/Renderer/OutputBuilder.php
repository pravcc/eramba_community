<?php
class OutputBuilder
{
	const CONTENT = '{{content}}';

	protected $_View = null;

	protected $_settings = [
	];

	public function __construct($View)
	{
		$this->_View = $View;
	}

	public function render()
	{
		return '';
	}

	public function fetchContent($template, $output)
	{
		if ($template === null) {
			return $output;
		}

		return str_replace(self::CONTENT, $output, $template);
	}

	public function apply($processor, $subject)
	{
		if ($processor instanceof RenderProcessor) {
			$processor = [$processor, 'render'];
		}

		$subject = (object) $subject;

		call_user_func_array($processor, [$this, $subject]);
	}

}