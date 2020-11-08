<?php
App::uses('Audit', 'ObjectVersion.Model');

class ObjectVersionAuditEvent {

	/**
	 * Holds event string.
	 * 
	 * @var array
	 */
	protected $_event = null;

	public function __construct($event) {
		$this->_event = $event;
	}

	public function event() {
		return $this->_event;
	}

	public function getLabel() {
		switch ($this->_event) {
			case Audit::EVENT_CREATE:
				return __('Create');
				break;

			case Audit::EVENT_EDIT:
				return __('Edit');
				break;

			case Audit::EVENT_DELETE:
				return __('Delete');
				break;

			case Audit::EVENT_RESTORE:
				return __('Restore');
				break;
		}

		return getEmptyValue(false);
	}

	public function isCreated() {
		return $this->_event == Audit::EVENT_CREATE;
	}

	public function isEdited() {
		return $this->_event == Audit::EVENT_EDIT;
	}

	public function isDeleted() {
		return $this->_event == Audit::EVENT_DELETE;
	}

	public function isRestored() {
		return $this->_event == Audit::EVENT_RESTORE;
	}

}
