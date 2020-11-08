<?php
App::uses('TableElement', 'Module/LimitlessTheme/Lib/Tables');

class TableRow extends TableElement
{
	/**
	 * TableColumns objects
	 */
	protected $columns = [];

	/**
	 * Tag for row
	 * @var string
	 */
	protected $tag = 'tr';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$row = $this->getStartTag();
		foreach ($this->columns as $column) {
			$row .= $column->render();
		}
		$row .= $this->getEndTag();

		return $row;
	}

	/**
	 * Get TableColumn instance
	 * @param  string           $name  Name of TableColumn (index by which user can reach the table column object)
	 * @return TableColumn         Returns TableColumn object
	 */
	public function getColumn($name)
	{
		if (!isset($this->columns[$name])) {
			throw new LimitlessThemeException(__('The column (%s) you\'re trying to use doesn\'t exists', $blockType));
		}

		return $this->columns[$name];
	}

	/**
	 * Create new TableColumn object
	 * @param  mixed            $options  Set values to params of the class
	 * @return TableColumn            Returns TableColumn object
	 */
	public function column($options = [])
	{
		if (!is_array($options)) {
			$options = [
				'content' => $options
			];
		}
		$column = $this->createObject('TableColumn', 'columns', $options);
	}

	/**
	 * Add more column objects at once
	 * @param  array  $columns Array of columns (one array with options per column)
	 * @return array           Array of TableColumn objects
	 */
	public function columns(array $columnsOptions)
	{
		$columns = [];
		foreach ($columnsOptions as $columnOptions) {
			$columns[] = $this->column($columnOptions);
		}

		return $columns;
	}

}