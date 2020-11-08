<?php
App::uses('TableElement', 'Module/LimitlessTheme/Lib/Tables');
App::uses('TableBlock', 'Module/LimitlessTheme/Lib/Tables');

class Table extends TableElement
{
	/**
	 * TableBlock header (head)
	 */
	protected $header = null;
	/**
	 * TableBlock body (body)
	 */
	protected $body = null;
	/**
	 * TableBlock footer (foot)
	 */
	protected $footer = null;

	protected $class = 'table datatable-scroll-y dataTable no-footer';
	protected $tag = "table";

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$table = $this->getStartTag();
		$table .= $this->header()->render();
		$table .= $this->body()->render();
		$table .= $this->footer()->render();
		$table .= $this->getEndTag();

		return $table;
	}

	/**
	 * Get existing or create new TableBlock object
	 * @param  string          $type      Type of block which you want to get
	 * @param  array           $options   Set values to params of the class
	 * @return TableBlock             Returns TableBlock object - existing or newly created
	 */
	protected function getBlock(string $type, array $options = [])
	{
		$this->isBlockTypeAllowed('table_' . $type);

		if (empty($this->{$type})) {
			$this->{$type} = new TableBlock('TableBlock' . ucfirst($type));
			$this->{$type}->setBlockType('table_' . $type);
			$this->{$type}->applyOptions($options);
		}

		return $this->{$type};
	}

	public function header(array $options = [])
	{
		return $this->getBlock('header', $options);
	}

	public function body(array $options = [])
	{
		return $this->getBlock('body', $options);
	}

	public function footer(array $options = [])
	{
		return $this->getBlock('footer', $options);
	}

	/**
	 * Set header
	 * @param TableBlock $header  Header block of the table
	 */
	public function setHeader(TableBlock $header)
	{
		$this->header = $header;
	}

	/**
	 * Set body
	 * @param TableBlock $body Body block of the table
	 */
	public function setBody(TableBlock $body)
	{
		$this->body = $body;
	}

	/**
	 * Set footer
	 * @param TableBlock $footer  Footer block of the table
	 */
	public function setFooter(TableBlock $footer)
	{
		$this->footer = $footer;
	}
}