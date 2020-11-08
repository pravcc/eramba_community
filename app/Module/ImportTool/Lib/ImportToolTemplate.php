<?php
App::uses('ImportToolBase', 'ImportTool.Lib');
App::uses('ImportToolArgument', 'ImportTool.Lib');
App::uses('ImportToolModule', 'ImportTool.Lib');

class ImportToolTemplate extends ImportToolBase {
	protected $_arguments = array();

	public function __construct(Model $Model) {
		parent::__construct($Model);

		$args = $this->_getModel()->getImportArgs();
		foreach ($args as $field => $arg) {
			$this->_arguments[] = new ImportToolArgument($this->_getModelName(), $field, $arg);
		}
	}

	public function convertDataToExport($modelData = array()) {

		$data = array();
		foreach ($modelData as $item) {
			$row = array();
			foreach ($this->getArguments() as $arg) {
				$row[] = $arg->convertToExport($item);
			}
			$data[] = $row;
		}
		
		return $data;
	}

	public function getArgumentPaths() {
		$paths = array();
		foreach ($this->getArguments() as $arg) {
			$paths[] = $arg->getHashPath();
		}

		return $paths;
	}

	public function getArguments() {
		return $this->_arguments;
	}

	public function getArgumentLabels() {
		$args = array();
		foreach ($this->getArguments() as $arg) {
			$args[] = $arg->getTemplateLabel();
		}

		return $args;
	}
}
