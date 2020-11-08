<?php
App::uses('CakeEventListener', 'Event');
App::uses('CakeObject', 'Core');

class FieldDataExtension extends CakeObject {

	public function setup(FieldDataEntity $Field, $config = []) {
	}

	public function initialize(FieldDataEntity $Field) {
	}

	public function beforeFind(FieldDataEntity $Field, $query) {
	}

	public function afterChange(FieldDataEntity $Field, $oldValue, $newValue) {
	}

}