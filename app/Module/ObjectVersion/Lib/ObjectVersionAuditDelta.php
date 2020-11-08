<?php
App::uses('ClassRegistry', 'Utility');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('Inflector', 'Utility');

class ObjectVersionAuditDelta {

	protected $_label = null;

	protected $_isHabtm = false;

	/**
	 * Holds original array data retrieved from Audit model.
	 * 
	 * @var array
	 */
	protected $_originalData = null;

	public function __construct($auditDelta = array(), $model) {
		$this->_originalData = $auditDelta;
		$this->_model = $model;
		
		$this->_setLabel();
	}

	protected function _getPropertyName() {
		return $this->_originalData['property_name'];
	}

	public function getToggleValue($identifier) {
		if (!empty($identifier)) {
			return __('Yes');
		}
		else {
			return __('No');
		}
	}

	/**
	 * @todo Migrate to using FieldData.
	 */
	protected function _setLabel() {
		if ($this->isEmpty()) {
			return true;
		}

		$Model = ClassRegistry::init($this->_model);

		if (!$Model->hasFieldDataEntity($this->_getPropertyName())) {
			// we force to not show the information
			$this->_originalData['new_value'] = false;
			return true;
		}

		$FieldDataEntity = $Model->getFieldDataEntity($this->_getPropertyName());

		if ($FieldDataEntity instanceof FieldDataEntity && !$FieldDataEntity->getAssociationKey()) {
			if ($FieldDataEntity->isToggle()) {
				$this->_label['old'] = $this->getToggleValue($this->getOldValue());
				$this->_label['new'] = $this->getToggleValue($this->getNewValue());

				return true;
			}

			$options = $FieldDataEntity->getFieldOptions();
			if (!empty($options)) {
				if (isset($options[$this->getOldValue()])) {
					$this->_label['old'] = $options[$this->getOldValue()];
				}
				else {
					$this->_label['old'] = false;
				}

				if (isset($options[$this->getNewValue()])) {
					$this->_label['new'] = $options[$this->getNewValue()];
				}
				else {
					$this->_label['new'] = false;
				}

				return true;
			}
		}

		foreach ($Model->belongsTo as $assocKey => $params) {
			if ($params['foreignKey'] == $this->_getPropertyName()) {
				if ($this->getOldValue()) {
					$this->_label['old'] = getItemTitle($Model->{$assocKey}, $this->getOldValue());
				}

				if ($this->getNewValue()) {
					$this->_label['new'] = getItemTitle($Model->{$assocKey}, $this->getNewValue());
				}

				// return true;
			}
		}

		//
		// Process UserField
		$processHabtm = true;
		if ($Model->Behaviors->enabled('UserFields') && $Model->Behaviors->UserFields->belongsAssociationToUserField($Model, $this->_getPropertyName())) {
			$processHabtm = false;
			$this->_isHabtm = true;

			$oldValue = static::buildHabtmValue($this->getOldValue());
			$newValue = static::buildHabtmValue($this->getNewValue());

			$unchanged = static::findUnchangedHabtmValues($oldValue, $newValue);
			$unchangedCount = count($unchanged);

			$userFieldOptions = $Model->getUsersGroupsOptions();
			if (count($oldValue) > $unchangedCount) {
				$this->_label['old'] = $this->processUserField(static::stripUnchangedValues($oldValue, $unchanged), $userFieldOptions);
			}

			if (count($newValue) > $unchangedCount) {
				$this->_label['new'] = $this->processUserField(static::stripUnchangedValues($newValue, $unchanged), $userFieldOptions);
			}
		}
		//
		
		if ($processHabtm) {
			foreach ($Model->hasAndBelongsToMany as $assocKey => $params) {
				if ($assocKey == $this->_getPropertyName()) {
					$this->_isHabtm = true;

					$oldValue = static::buildHabtmValue($this->getOldValue());
					$newValue = static::buildHabtmValue($this->getNewValue());

					$unchanged = static::findUnchangedHabtmValues($oldValue, $newValue);
					$unchangedCount = count($unchanged);

					if (count($oldValue) > $unchangedCount) {
						$this->_label['old'] = $this->processHabtmField(
							$Model->{$assocKey}->name,
							static::stripUnchangedValues($oldValue, $unchanged)
						);
					}

					if (count($newValue) > $unchangedCount) {
						$this->_label['new'] = $this->processHabtmField(
							$Model->{$assocKey}->name,
							static::stripUnchangedValues($newValue, $unchanged)
						);
					}
				}
			}
		}
	}

	protected function processUserField($userFieldIds, $userFieldOptions)
	{
		$label = false;
		if (!empty($userFieldIds)) {
			$labelTemp = [];
			foreach ($userFieldIds as $id) {
				if (isset($userFieldOptions[$id])) {
					$labelTemp[] = $userFieldOptions[$id];
				}
			}
			$label = implode(', ', $labelTemp);
		}

		return $label;
	}

	public static function stripUnchangedValues($haystack, $strip) {
		foreach ($strip as $s) {
			$key = array_search($s, $haystack);
			if ($key !== false) {
				unset($haystack[$key]);
			}
		}

		return $haystack;
	}

	public static function buildHabtmValue($value) {
		if (empty($value)) {
			return array();
		}

		return explode(',', $value);
	}

	public static function findUnchangedHabtmValues($val1, $val2) {
		return array_intersect($val1, $val2);
	}

	protected function processHabtmField($assocModel, $ids) {
		if (empty($ids)) {
			return false;
		}

		$titles = array();
		foreach ($ids as $id) {
			$titles[] = getItemTitle($assocModel, $id);
		}

		return implode(', ', $titles);
	}

	public function getFieldLabel($model) {
		$Model = ClassRegistry::init($model);

		if ($Model->hasFieldDataEntity($this->_getPropertyName())) {
			$label = $Model->getFieldDataEntity($this->_getPropertyName())->getLabel();
		}
		else {
			$label = Inflector::humanize($this->_getPropertyName());
		}

		return $label;
	}

	public function isEmpty() {
		return !$this->hasNewValue() && !$this->hasOldValue();
	}

	public function hasOldValue() {
		return !empty($this->_originalData['old_value']);
	}

	public function hasNewValue() {
		// lets try this
		// return $this->_originalData['new_value'] !== null;
		return !empty($this->_originalData['new_value']);
	}

	public function getOldValue() {
		$val = $this->_originalData['old_value'];

		return $val;
	}

	public function getNewValue() {
		$val = $this->_originalData['new_value'];

		return $val;
	}

	public function getOldLabel() {
		if (empty($this->_label['old'])) {
			if ($this->_isHabtm) {
				return null;
			}

			return $this->getOldValue();
		}

		return $this->_label['old'];
	}

	public function getNewLabel() {
		if (empty($this->_label['new'])) {
			if ($this->_isHabtm) {
				return null;
			}

			return $this->getNewValue();
		}
		
		return $this->_label['new'];
	}

}
