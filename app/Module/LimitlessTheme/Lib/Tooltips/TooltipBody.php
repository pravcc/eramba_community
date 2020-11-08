<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipBody extends TooltipElement
{
	/**
	 * TooltipRows objects
	 */
	protected $rows = [];

	protected $tag = 'div';
	protected $class = 'modal-body';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$body = $this->getStartTag();
		foreach ($this->rows as $row) {
			$body .= $row->render();
		}
		$body .= $this->getEndTag();

		return $body;
	}

	/**
	 * Get TooltipRow instance
	 * @param  string         $name   Name of TooltipRow (index by which user can reach the tooltip row object)
	 * @return TooltipRow
	 */
	public function getRow($name)
	{
		if (!isset($this->rows[$name])) {
			throw new LimitlessThemeException(__('The row (%s) you\'re trying to use doesn\'t exists', $name));
		}

		return $this->rows[$name];
	}

	/**
	 * Create new TooltipRow object
	 * @param  array          $options   Set values to params of the class
	 * @return TooltipRow              Returns TooltipRow object - existing or newly created
	 */
	public function row(array $options = [])
	{
		return $this->createObject('TooltipRow', 'rows', $options);
	}

}