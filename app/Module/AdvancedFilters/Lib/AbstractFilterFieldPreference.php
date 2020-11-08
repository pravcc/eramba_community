<?php
abstract class AbstractFilterFieldPreference
{
	public $alias = null;
	
	protected $_value = null;

	public function __construct($value = null)
	{
		$this->_value = $value;
	}

	public function getValue()
	{
		return $this->_value;
	}
}