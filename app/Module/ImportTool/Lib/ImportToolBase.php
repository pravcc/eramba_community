<?php
App::uses('ClassRegistry', 'Utility');

/**
 * Base generic class for Import Tool functionality.
 */
class ImportToolBase {
	/**
	 * Model name.
	 * 
	 * @var string
	 */
	protected $_model = null;

	/**
	 * We set a $_model name string for later use.
	 * 
	 * @param string $_model Model name.
	 */
	public function __construct($Model) {
		if ($Model instanceof Model) {
			$this->_model = $Model->name;
		}
		else {
			$this->_model = $Model;
		}
	}

	protected function _getModelName() {
		return $this->_model;
	}

	/**
	 * Get instance of a Model for this data object.
	 * 
	 * @return Model
	 */
	protected function _getModel() {
		return ClassRegistry::init($this->_model);
	}

	/**
	 * Public alias of ImportToolBase::_getModel();
	 *
	 * @return Model
	 */
	public function getModel() {
		return ClassRegistry::init($this->_model);
	}
}