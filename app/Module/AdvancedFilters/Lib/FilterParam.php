<?php
App::uses('Hash', 'Utility');
App::uses('AdvancedFilterUserParam', 'AdvancedFilters.Model');

class FilterParam {

	/**
	 * Basic field key.
	 * 
	 * @var string
	 */
	protected $_key = null;

	protected $_type = null;

	protected $_value = null;

	public function __construct($type = AdvancedFilterUserParam::TYPE_GENERAL, $key, $value = null)
	{
		$this->_type = $type;
		$this->_key = $key;
		$this->_value = $value;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function getType()
	{
		return $this->_type;
	}

	public function getKey()
	{
		return $this->_key;
	}

}
