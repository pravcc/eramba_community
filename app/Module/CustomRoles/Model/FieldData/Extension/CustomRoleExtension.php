<?php
App::uses('FieldDataExtension', 'FieldData.Model/FieldData');

class CustomRoleExtension extends FieldDataExtension {

	public function setup(FieldDataEntity $Field, $config = []) {
		// debug($Field->alias);
	}

	public function initialize(FieldDataEntity $Field) {
		$this->Field = $Field;
	}

	public function beforeFind(FieldDataEntity $Field, $query) {
		return true;
	}

	public function checkPermission($requestor, $object) {
		$value = $object->field($this->Field->getFieldName());
		
		return $requestor->id == $value;
	}

	public function afterChange(FieldDataEntity $Field, $oldValue, $newValue) {
		// debug(Debugger::exportVar(func_get_args()));exit;
	}

}