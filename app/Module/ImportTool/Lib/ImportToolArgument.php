<?php
App::uses('ImportToolBase', 'ImportTool.Lib');

class ImportToolArgument extends ImportToolBase {
	protected $_field;
	protected $_name;
	protected $_relatedModel;
	protected $_callback = array();
	protected $_headerHint;
	protected $_headerTooltip;
	protected $_objectAutoFind;
	protected $_objectAutoFindClass;
	protected $_isAssociated = false;
	protected $_association = array();

	public function __construct($Model, $field, $options = array()) {
		parent::__construct($Model);

		$options = am(array(
			'name' => false,
			'model' => false,
			'callback' => array(),
			'headerHint' => false,
			'headerTooltip' => false,
			'objectAutoFind' => false,
			'objectAutoFindClass' => false,
		), $options);

		$this->_field = $field;
		$this->_name = $options['name'];
		$this->_relatedModel = $options['model'];
		$this->_callback = $options['callback'];
		$this->_headerHint = $options['headerHint'];
		$this->_headerTooltip = $options['headerTooltip'];
		$this->_objectAutoFind = $options['objectAutoFind'];
		$this->_objectAutoFindClass = $options['objectAutoFindClass'];

		if (!empty($this->_relatedModel)) {
			$this->_isAssociated = true;
			$this->_association = $this->_getModel()->getAssociated($this->_relatedModel);
		}

		return $this;
	}

	/**
	 * Get the argument's association name.
	 * 
	 * @return string Association name.
	 */
	public function getAssocation() {
		return $this->_association;
	}

	public function getAssocationModelName()
	{
		return $this->_relatedModel;
	}

	public function getHashPath() {
		if ($this->isAssociated()) {
			if ($this->_association['association'] == 'hasAndBelongsToMany') {
				$path = $this->_relatedModel . '.{n}.' . $this->_getModel()->{$this->_relatedModel}->primaryKey;
				return $path;
			}

			if ($this->_association['association'] == 'hasMany') {
				$path = $this->_relatedModel . '.{n}.' . $this->_getModel()->{$this->_relatedModel}->displayField;
				return $path;
			}
		}

		$path = $this->getField();
		if (!$this->_containsModel($path)) {
			$path = $this->_getModelName() . '.' . $path;
		}

		return $path;
	}

	public function convertToImport($value) {
		// handle blank spaces that happen when editing file in a non-text-editor mode (excel, ..etc)
		$value = trim($value);

		// for associated model values we treat ID zero as nothing
		if ($this->isAssociated() && ($value === '0' || $value === 0)) {
			$value = '';
		}

		if ($this->isAssociated()) {
			if ($this->_association['association'] == 'hasAndBelongsToMany') {
				$value = ImportToolModule::explodeValues($value);
			}

			if ($this->_association['association'] == 'hasMany') {
				$value = ImportToolModule::explodeValues($value);
			}
		}

		if (!empty($this->_callback['beforeImport'])) {
			$value = call_user_func_array($this->_callback['beforeImport'], array($value));
		}

		return $value;
	}

	public function convertToExport($item) {
		if (!empty($this->_callback['beforeExport'])) {
			$value = call_user_func_array($this->_callback['beforeExport'], array($item));
		}
		else {
			$value = Hash::extract($item, $this->getHashPath());
			$value = ImportToolModule::buildValues($value);
		}

		return $value;
	}

	/**
	 * Checks if current field is required via model validation rules.
	 */
	public function isRequired() {
		$field = $this->_getModel()->validator()->getField($this->_field);
		if ($field === null) {
			return false;
		}

		$rules = $field->getRules();
		foreach ($rules as $rule) {
			if ($rule->isRequired()) {
				return true;
			}
		}

		return false;
	}

	public function getLabel() {
		$isRequired = $this->isRequired();
		$arg1 = $this->_name;

		if ($this->isAssociated()) {
			if (in_array($this->_association['association'], array('hasAndBelongsToMany', 'belongsTo'))) {
				if (!empty($this->_headerHint)) {
					$arg2 = $this->_headerHint;
				}
				elseif ($this->objectAutoFind()) {
					$arg2 = __('Object Name');
				}
				else {
					$arg2 = __('ID');
				}
			}

			if ($this->_association['association'] == 'hasMany') {
				if (!empty($this->_headerHint)) {
					$arg2 = $this->_headerHint;
				}
				else {
					$arg2 = $this->_getModel()->{$this->_relatedModel}->displayField;
				}
			}

			if ($isRequired) {
				$arg2 = sprintf('%s, %s', $arg2, __('Mandatory'));
			}

			return sprintf('%s (%s)', $arg1, $arg2);
		}
		elseif ($this->objectAutoFind()) {
			$arg2 = __('Object Name');
			
			if ($isRequired) {
				$arg2 = sprintf('%s, %s', $arg2, __('Mandatory'));
			}

			return sprintf('%s (%s)', $arg1, $arg2);
		}

		if ($isRequired) {
			$arg1 = sprintf('%s (%s)', $arg1, __('Mandatory'));
		}

		return $arg1;
	}

	/**
	 * Label used for export to .csv file.
	 */
	public function getTemplateLabel() {
		return sprintf('%s (%s)', $this->_name, $this->getHeaderTooltip());
	}

	public function getHeaderTooltip() {
		return $this->_headerTooltip;
	}

	public function getField() {
		return $this->_field;
	}

	public function objectAutoFind() {
		return $this->_objectAutoFind;
	}

	public function objectAutoFindClass() {
		return $this->_objectAutoFindClass;
	}

	public function isAssociated() {
		return $this->_isAssociated;
	}

	protected function _containsModel($field) {
		return (strpos($field, '.') !== false);
	}
}
