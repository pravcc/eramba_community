<?php
class ImportToolCsv {
	protected $_filePath;
	protected $_data = [];
	public $errors = array();

	public function __construct($filePath) {
		// file extension check
		// $extension = pathinfo($filePath, PATHINFO_EXTENSION);
		
		$this->_filePath = $filePath;
		$this->_parseFile();
	}

	private function _parseFile() {
		if ($this->_filePath !== false 
			&& $this->_filePath !== null 
			&& $this->_filePath !== ''
			&& file_exists($this->_filePath)
			&& is_readable($this->_filePath)
			&& ($fp = fopen($this->_filePath, 'rb')) !== false
	 	) {
			while(!feof($fp)) {
				$fgetcsv = fgetcsv($fp);
				if (!empty($fgetcsv)) {
					$this->_data[] = $fgetcsv;
				}
			}
			fclose($fp);
		}
		else {
			$this->errors[] = __('Error reading from the file');
		}
	}

	public function getData() {
		return $this->_data;
	}

	public function getErrors() {
		return $this->errors;
	}

}
