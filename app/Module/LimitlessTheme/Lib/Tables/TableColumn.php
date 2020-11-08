<?php
App::uses('TableElement', 'Module/LimitlessTheme/Lib/Tables');

class TableColumn extends TableElement
{
	/**
	 * Tag for column
	 * Options:
	 * 	- th (head)
	 * 	- td (body, footer)
	 * @var string
	 */
	protected $tag = 'td';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$column = $this->getStartTag();
		$column .= $this->content;
		$column .= $this->getEndTag();

		return $column;
	}

	public function setBlockType(string $type)
	{
		parent::setBlockType($type);

		switch ($type) {
			case 'table_header':
				$this->setTag('th');
				break;
			case 'table_body':
				$this->setTag('td');
				break;
			case 'table_footer':
				$this->setTag('td');
				break;
		}
	}
}