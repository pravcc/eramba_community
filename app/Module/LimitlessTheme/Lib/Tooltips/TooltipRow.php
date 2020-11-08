<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipRow extends TooltipElement
{
	/**
	 * TooltipColumns objects
	 */
	protected $columns = [];

	/**
	 * Tag for row
	 * @var string
	 */
	protected $tag = 'div';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$row = $this->getStartTag();
		$index = 0;
		foreach ($this->columns as $column) {
			if (strpos($column->getClass(), 'col-') === false) {
				$column->setClass('col-md-' . floor(12 / count($this->columns)));
			}
			$row .= $column->render();

			if (++$index == 12) {
				break;
			}
		}
		$row .= $this->getEndTag();

		return $row;
	}

	/**
	 * Get TooltipColumn instance
	 * @param  string           $name  Name of TooltipColumn (index by which user can reach the tooltip column object)
	 * @return TooltipColumn         Returns TooltipColumn object
	 */
	public function getColumn($name)
	{
		if (!isset($this->columns[$name])) {
			throw new LimitlessThemeException(__('The column (%s) you\'re trying to use doesn\'t exists', $name));
		}

		return $this->columns[$name];
	}

	/**
	 * Create new TooltipColumn object
	 * @param  array            $options  Set values to params of the class
	 * @return TooltipColumn            Returns TooltipColumn object
	 */
	public function column(array $options = [])
	{
		return $this->createObject('TooltipColumn', 'columns', $options);
	}
}