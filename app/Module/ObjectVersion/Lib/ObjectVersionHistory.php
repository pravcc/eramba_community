<?php
App::uses('ClassRegistry', 'Utility');
App::uses('Audit', 'ObjectVersion.Model');
App::uses('ObjectVersionAudit', 'ObjectVersion.Lib');

class ObjectVersionHistory {
	/**
	 * Audit model instance.
	 * 
	 * @var Audit
	 */
	protected $Audit = null;

	/**
	 * Holds current object information.
	 */
	protected $_model, $_foreignKey = null;

	/**
	 * Holds original array data retrieved from Audit model.
	 * 
	 * @var array
	 */
	protected $_originalData = null;

	/**
	 * Hold history versioning.
	 * 
	 * @var ObjectVersionAudit
	 */
	protected $_audits = null;

	/**
	 * Lets setup the history class with a certain object.
	 * 
	 * @param string $model      Model name.
	 * @param int    $foreignKey Entity ID.
	 */
	public function __construct($model, $foreignKey) {
		$this->Audit = ClassRegistry::init('ObjectVersion.Audit');
		$this->_model = $model;
		$this->_foreignKey = $foreignKey;
		
		$this->_originalData = $this->Audit->getHistory($model, $foreignKey);
		
		$this->Audit = null;

		$this->_setAudit();

		$this->_originalData = null;

	}

	protected function _setAudit() {
		foreach ($this->_originalData as $index => $audit) {
			$this->_audits[] = new ObjectVersionAudit($audit, $this);
		}
	}

	public function getAudits() {
		return $this->_audits;
	}

	public function getModel() {
		return $this->_model;
	}

	/**
	 * Returns the current history array.
	 * 
	 * @return array History of the object.
	 */
	public function getHistoryArray() {
		return $this->_originalData;
	}
}
