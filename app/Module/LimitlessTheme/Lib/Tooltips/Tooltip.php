<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class Tooltip extends TooltipElement
{
	/**
	 * TooltipHeader header
	 */
	protected $header = null;
	/**
	 * TooltipBody body
	 */
	protected $body = null;
	/**
	 * TooltipFooter footer
	 */
	protected $footer = null;

	protected $class = 'modal-content modal-content-custom';
	protected $tag = "div";

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$tooltip = $this->getStartTag();
		$tooltip .= $this->header()->render();
		$tooltip .= $this->body()->render();
		$tooltip .= $this->footer()->render();
		$tooltip .= $this->getEndTag();

		return $tooltip;
	}

	/**
	 * Get existing or create new Tooltip(Header|Body|Footer) object
	 * @param  string          $type        Type of block which you want to get
	 * @param  array           $options     Set values to params of the class
	 * @return Tooltip(Header|Body|Footer)  Returns existing or newly created object
	 */
	private function getBlock(string $type, array $options = [])
	{
		if (empty($this->{$type})) {
			$class = 'Tooltip' . ucfirst($type);
			$this->{$type} = new $class($class);
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
	 * @param TooltipHeader $header  Header of the tooltip
	 */
	public function setHeader(TooltipHeader $header)
	{
		$this->header = $header;
	}

	/**
	 * Set body
	 * @param TooltipBody $body Body of the tooltip
	 */
	public function setBody(TooltipBody $body)
	{
		$this->body = $body;
	}

	/**
	 * Set footer
	 * @param TooltipFooter $footer  Footer of the tooltip
	 */
	public function setFooter(TooltipFooter $footer)
	{
		$this->footer = $footer;
	}
}