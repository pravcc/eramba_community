<?php
/**
 * @package       Workflows.Lib
 */

class WorkflowObject implements Iterator {

	public $data = null;
	private $position;

	public function __construct($data) {
		$this->data = $data;

		if (is_array($this->data) && isset($this->data[0])) {
			$this->position = 0;
		}
	}

	public function __get($name) {
		if (isset($this->data[$name])) {
			if (is_array($this->data[$name])) {
				return new WorkflowObject($this->data[$name]);
			}

			return $this->data[$name];
		}
	}

	public function __set($name, $value) {
		if (isset($this->data[$name])) {
			$this->data[$name] = $value;
		}
	}

	public function rewind() {
		$this->position = 0;
	}

	public function current() {
		return new WorkflowObject($this->data[$this->position]);
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		++$this->position;
	}

	public function valid() {
		return isset($this->data[$this->position]);
	}

	public function getData() {
		return $this->data;
	}

	public function isEmpty() {
		return empty($this->data);
	}


}
