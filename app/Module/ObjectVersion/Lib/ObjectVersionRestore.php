<?php
App::uses('ClassRegistry', 'Utility');
App::uses('Audit', 'ObjectVersion.Model');
App::uses('ObjectVersionAudit', 'ObjectVersion.Lib');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class ObjectVersionRestore {
	protected $_auditId;
	protected $_model;
	protected $_foreignKey;
	protected $_jsonObject;
	protected $_data;
	protected $_valid;
	protected $_hasChanges;
	protected $_excludedBehaviors = ['FileValidation', 'Attachment'];
	protected $_excludeBehaviors = false;
	protected $_result = null;

	/**
	 * Lets setup the restore class for a certain revision.
	 * 
	 * @param string $auditId  Audit ID.
	 */
	public function __construct($auditId)
	{
		$this->_auditId = $auditId;
		$Audit = ClassRegistry::init('ObjectVersion.Audit');

		$audit = $Audit->find('first', array(
			'conditions' => array(
				'Audit.id' => $auditId
			),
			'recursive' => -1
		));

		$this->_model = $audit['Audit']['model'];
		$this->_foreignKey = $audit['Audit']['entity_id'];
		$this->_jsonObject = $audit['Audit']['json_object'];
		$this->_data = json_decode($this->_jsonObject, true);

		$this->_buildData();
	}

	/**
	 * We prepare the data for restoring a revision.
	 */
	protected function _buildData()
	{
		$_m = _getModelInstance($this->_model);

		if ($_m->Behaviors->enabled('AuditLog.Auditable')) {
			$ignore = $_m->getAuditIgnoredFields();
		}
		else {// fallback
			$ignore = ['workflow_status', 'workflow_owner_id'];
		}

		foreach ($this->_data[$this->_model] as $propertyName => $value) {
			if (in_array($propertyName, $ignore)) {
				continue;
			}

			if (!$_m->hasFieldDataEntity($propertyName)) {
				continue;
			}

			$FieldDataEntity = $_m->getFieldDataEntity($propertyName);

			if ($FieldDataEntity->isHabtm()) {
				$this->_data[$this->_model][$propertyName] = FieldDataEntity::parseHabtmData($value);

				// lets validate if IDs actually exists in the database
				if (is_array($this->_data[$this->_model][$propertyName])) {
					// lets find the list of existing ones in the database
					$habtmModel = $_m->{$propertyName};

					$loadSoftDelete = false;
					if ($habtmModel->Behaviors->loaded('SoftDelete')) {
						$loadSoftDelete = true;
						$habtmModel->softDelete(false);
					}

					$habtmList = $habtmModel->find('list', [
						'conditions' => [
							$_m->{$propertyName}->alias . '.' . $_m->{$propertyName}->primaryKey => $this->_data[$this->_model][$propertyName]
						],
						'fields' => [
							$_m->{$propertyName}->primaryKey
						],
						'recursive' => -1
					]);

					if ($loadSoftDelete) {
						$habtmModel->softDelete(false);
					}

					foreach ($this->_data[$this->_model][$propertyName] as $key => $propertyId) {
						if (!in_array($propertyId, $habtmList)) {
							unset($this->_data[$this->_model][$propertyName][$key]);
						}
					}
				}
			}
		}

		// lets unset the primary key to not cause conflict when saving the data
		// unset($this->_data[$this->_model][$_m->primaryKey]);

		return $this;
	}

	/**
	 * Validation was successfull before the actual save.
	 * 
	 * @return boolean True on success, False otherwise.
	 */
	public function isRestoredDataValid() {
		return $this->_valid;
	}

	/**
	 * Was there one or more value changes made to the object during this save.
	 * 
	 * @return boolean True if there was at least one change of value, False otherwise.
	 */
	public function hasChanges() {
		return $this->_hasChanges;
	}

	public function getForeignKey()
	{
		return $this->_foreignKey;
	}

	public function getData()
	{
		return $this->_data;
	}

	public function getResult()
	{
		return $this->_result;
	}

	public function restore($saveMethod, $data = null, $options = [])
	{
		if ($data === null) {
			$data == $this->getData();
		}

		$ret = (boolean) call_user_func([ClassRegistry::init($this->_model), $saveMethod], $data, $options);

		$this->_result = $ret;

		return $ret;
	}

	/**
	 * Do the restore of the object version.
	 * 
	 * @return bool True on success or false on fail.
	 */
	public function beforeRestore()
	{
		$_m = _getModelInstance($this->_model);
		
		if ($_m->Behaviors->loaded('SoftDelete')) {
			$_m->softDelete(false);
		}

		$_m->objectRestored = $this->_auditId;

		$this->_excludeBehaviors = !empty(array_intersect($_m->Behaviors->enabled(), $this->_excludedBehaviors)) ? true : false;

		if ($this->_excludeBehaviors) {
			$_m->Behaviors->disable($this->_excludedBehaviors);
		}

		$this->_valid = $_m->validateAssociated($this->_data);
	}

	public function afterRestore()
	{
		$_m = _getModelInstance($this->_model);

		if ($this->_excludeBehaviors) {
			$_m->Behaviors->enable($this->_excludedBehaviors);
		}

		$this->_hasChanges = $_m->objectVersionHasChanges;

		unset($_m->objectRestored);

		if ($_m->Behaviors->loaded('SoftDelete')) {
			$_m->softDelete(true);
		}
	}
}
