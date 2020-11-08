<?php
class Crumb
{
	protected $_title = null;

	protected $_link = null;

	public function __construct($title = null, $link = null)
	{
		$this->_title = $title;
		$this->_link = $link;
	}

	public function title($title = null)
	{
		if ($title !== null) {
			$this->_title = $title;
		}

		return $this->_title;
	}

	public function link($link = null)
	{
		if ($link !== null) {
			$this->_link = $link;
		}

		return $this->_link;
	}
}