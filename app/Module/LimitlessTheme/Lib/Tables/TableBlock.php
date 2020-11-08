<?php
App::uses('TableElement', 'Module/LimitlessTheme/Lib/Tables');

class TableBlock extends TableElement
{
	/**
	 * TableRows objects
	 */
	protected $rows = [];

	/**
	 * Tag for block
	 * Options:
	 * 	- thead
	 * 	- tbody
	 * 	- tfoot
	 * @var string
	 */
	protected $tag = 'tbody';

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
			case 'table_header':
				$this->setTag('thead');
				break;
			case 'table_body':
				$this->setTag('tbody');
				break;
			case 'table_footer':
				$this->setTag('tfoot');
				break;
		}
	}

	/**
	 * Get TableRow instance
	 * @param  string         $name   Name of TableRow (index by which user can reach the table row object)
	 * @return TableRow
	 */
	public function getRow($name)
	{
		if (!isset($this->rows[$name])) {
			throw new LimitlessThemeException(__('The block (%s) you\'re trying to use doesn\'t exists', $blockType));
		}

		return $this->rows[$name];
	}

	/**
	 * Create new TableRow object
	 * @param  array          $options   Set values to params of the class
	 * @return TableRow              Returns TableRow object - existing or newly created
	 */
	public function row(array $options = [])
	{
		return $this->createObject('TableRow', 'rows', $options);
	}

}