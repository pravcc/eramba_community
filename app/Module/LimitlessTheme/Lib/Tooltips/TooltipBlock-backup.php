<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipBlock extends TooltipElement
{
	/**
	 * TooltipRows objects
	 */
	protected $rows = [];

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$block = $this->getStartTag();
		foreach ($this->rows as $row) {
			$block .= $row->render();
		}
		$block .= $this->getEndTag();

		return $block;
	}

	public function setBlockType(string $type)
	{
		parent::setBlockType($type);

		switch ($type) {
			case 'tooltip_header':
				$this->setClass('modal-header');
				break;
			case 'tooltip_body':
				$this->setClass('modal-body');
				break;
			case 'tooltip_footer':
				$this->setClass('modal-footer');
				break;
		}
	}

	/**
	 * Get TooltipRow instance
	 * @param  string         $name   Name of TooltipRow (index by which user can reach the tooltip row object)
	 * @return TooltipRow
	 */
	public function getRow($name)
	{
		if (!isset($this->rows[$name])) {
			throw new LimitlessThemeException(__('The block (%s) you\'re trying to use doesn\'t exists', $blockType));
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