<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipHeader extends TooltipElement
{
	/**
	 * TooltipHeadings objects
	 */
	protected $headings = [];

	protected $tag = 'div';
	protected $class = 'modal-header bg-primary';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$header = $this->getStartTag();
		foreach ($this->headings as $heading) {
			$header .= $heading->render();

			// We need only one loop because only one heading can be in the header of the modal
			break;
		}
		$header .= $this->getEndTag();

		return $header;
	}

	/**
	 * Create new TooltipHeading object
	 * @param  array          $options   Set values to params of the class
	 * @return TooltipHeading              Returns TooltipHeading object - existing or newly created
	 */
	public function heading(string $content, array $options = [])
	{
		$options['content'] = $content;
		$options['tag'] = isset($options['tag']) ? $options['tag'] : 'h5';
		return $this->createObject('TooltipHeading', 'headings', $options);
	}

}