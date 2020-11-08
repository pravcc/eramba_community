<?php
App::uses('FieldDataExtension', 'FieldData.Model/FieldData');

class TestFieldDataExtension extends FieldDataExtension {
	
	public function setup() {
		// debug("setup");
	}

	public function initialize(FieldDataEntity $Field) {
		$this->Field = $Field;
		// debug("initialize");
		// debug($Field->config());
	}

	public function testOptions() {
		// debug(stacktrace());
		return $this->Field->getFieldOptions();
	}

	public function beforeFind(FieldDataEntity $Field, $query) {
		$query['conditions'] = [
			'id' => [1,2,3,4,5,6]
		];

		return $query;
	}

}