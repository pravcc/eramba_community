<?php
App::uses('ObjectVersionAuditDelta', 'ObjectVersion.Lib');
App::uses('ObjectVersionAuditEvent', 'ObjectVersion.Lib');
App::uses('CakeTime', 'Utility');

class ObjectVersionAudit {

	/**
	 * Holds original array data retrieved from Audit model.
	 * 
	 * @var array
	 */
	protected $_originalData = null;

	/**
	 * Hold audit delta versioning.
	 * 
	 * @var ObjectVersionAudit
	 */
	protected $_auditDeltas = null;

	/**
	 * Hold audit event class.
	 * 
	 * @var ObjectVersionAuditEvent
	 */
	protected $_auditEvent = null;

	public function __construct($audit = array(), ObjectVersionHistory &$ObjectVersionHistory = null) {
		$this->_originalData = $audit;
		$this->_model = $this->_originalData['Audit']['model'];

		$this->_setAuditDeltas();
	}

	/**
	 * Crates Audit Delta classes for the curent Audit.
	 */
	protected function _setAuditDeltas() {
		foreach ($this->_originalData['AuditDelta'] as $index => $auditDelta) {
			$this->_auditDeltas[] = new ObjectVersionAuditDelta($auditDelta, $this->_model);
		}
	}

	public function getEventClass() {
		return new ObjectVersionAuditEvent($this->_originalData['Audit']['event']);
	}

	protected function _getDate() {
		return $this->_originalData['Audit']['created'];
	}

	public function getTimeAgo() {
		return CakeTime::timeAgoInWords($this->_getDate());
	}

	public function getDescription() {
		return $this->_originalData['Audit']['description'];
	}

	public function getVersion() {
		return $this->_originalData['Audit']['version'];
	}

	public function hasRestoredVersion() {
		return $this->_originalData['Audit']['restore_id'] !== null && $this->getRestoredVersion();
	}

	public function getRestoredVersion() {
		return $this->_originalData['Restore']['version'];
	}

	public function getId() {
		return $this->_originalData['Audit']['id'];
	}

	/**
	 * Check if this Audit has some Audit Deltas (changes).
	 * 
	 * @return boolean True if it has changes, false otherwise.
	 */
	public function hasAuditDelta($skipEmpty = false) {
		$_deltas = $this->getAuditDeltas($skipEmpty);

		return !empty($_deltas);
	}

	/**
	 * Get Audit Deltas as array of objects.
	 * 
	 * @return array Of Audit Delta objects (changes).
	 */
	public function getAuditDeltas($skipEmpty = false) {
		if (empty($this->_auditDeltas)) {
			return false;
		}

		if (empty($skipEmpty)) {
			return $this->_auditDeltas;
		}
		
		return array_filter($this->_auditDeltas, array($this, 'filterEmptyDeltas'));
	}

	public function filterEmptyDeltas($item) {
		return !$item->isEmpty();
	}

}
