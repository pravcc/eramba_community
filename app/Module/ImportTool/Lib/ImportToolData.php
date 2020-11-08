<?php
App::uses('ImportToolBase', 'ImportTool.Lib');
App::uses('ImportToolArgument', 'ImportTool.Lib');
App::uses('ImportToolRow', 'ImportTool.Lib');

class ImportToolData extends ImportToolBase {
	protected $_originalData = array();
	protected $_formattedData = array();
	protected $_isImportable = null;

	protected $_strictValidation = false;

	protected $_arguments = array();
	protected $_data = array();

	// holds count of defined arguments in a model, used in comparison with parsed file data
	protected $_argumentsCount = null;

	protected $_nonUtf8Character = false;

	public function __construct($Model, $_originalData, $_strictValidation = false) {
		parent::__construct($Model);

		$this->_originalData = $_originalData;
		$this->_strictValidation = $_strictValidation;

		$this->_validateOriginalDataEncoding();

		$this->_setArguments();
		$this->_setData();
		$this->_setDataValidations();
	}

	protected function _validateOriginalDataEncoding()
	{
		foreach ($this->_originalData as $row => $rowData) {
			if (!is_array($rowData)) {
				continue;
			}

			foreach ($rowData as $item) {
				if (!mb_check_encoding($item, 'UTF-8')) {
					$this->_nonUtf8Character = true;
				}
			}
		}
	}

	protected function _setArguments() {
		$args = $this->_getModel()->getImportArgs();

		foreach ($args as $field => $arg) {
			$this->_arguments[] = new ImportToolArgument($this->_getModelName(), $field, $arg);
		}

		$this->_argumentsCount = count($this->_arguments);
	}

	protected function _setData() {
		foreach ($this->_originalData as $row => $rowData) {
			$this->_data[] = new ImportToolRow($this, $row, $rowData, $this->_strictValidation);
			$this->_formattedData[] = end($this->_data)->getData();
		}
	}

	protected function _setDataValidations() {
		$this->_isImportable = true;
		foreach ($this->getData() as $ImportToolRow) {
			$this->_isImportable &= $ImportToolRow->isImportable();
		}
	}

	/**
	 * Process for importing provided data into the system.
	 * 
	 * @return boolean True on success, otherwise false.
	 * @deprecated
	 */
	public function doImport() {
		if (!$this->isImportable()) {
			return false;
		}
	}

	public function isImportable() {
		return $this->_isImportable;
	}

	public function getArguments($index = null) {
		if (empty($index)) {
			return $this->_arguments;
		}

		return $this->_arguments[$index];
	}

	public function getArgumentsCount() {
		return $this->_argumentsCount;
	}

	public function getData() {
		return $this->_data;
	}

	public function getDataArray() {
		return $this->_formattedData;
	}

	public function getImportableDataArray() {
		$data = $this->getData();

		$importableArr = array();
		foreach ($data as $item) {
			$importableArr[] = $item->getImportableDataArray();
		}

		return $importableArr;
	}

	public function hasNonUtf8Character()
	{
		return $this->_nonUtf8Character;
	}

}
