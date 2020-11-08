<?php
class FieldGroupEntity {
	protected $_config = array();

	protected $_defaults = array(
		'label' => null,
		'order' => 10,
		'navItemOptions' => []
	);

	protected $_fields = array();

	public function __construct($config) {
		$config = am($this->_defaults, $config);
		$this->_config = $config;
		$this->_key = $config['__key'];
	}

	public function getKey() {
		return $this->_key;
	}

	public function getLabel() {
		return $this->_config['label'];
	}

	public function getOrder() {
		return $this->_config['order'];
	}

	public function getNavItemOptions() {
		return $this->_config['navItemOptions'];
	}

	/**
	 * Get Group class for the current FieldData entity.
	 * 
	 * @return FieldGroupEntity
	 */
	public function getFields() {
		
	}
}