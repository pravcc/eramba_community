<?php
App::uses('FieldDataHelper', 'FieldData.View/Helper');
App::uses('CustomValidatorField', 'CustomValidator.Model');

class CustomValidatorFieldsHelper extends FieldDataHelper {

	public function getCustomOptions($Field) {
		$options = [];
		$conditions = [];

		$customValidator = $this->_View->get('customValidator');
		if (empty($customValidator)) {
			return $options;
		}

		$fieldName = $Field->getFieldName();

		foreach ($customValidator as $validator) {
			if (isset($validator['fields'][$fieldName]) && $validator['fields'][$fieldName] === CustomValidatorField::DISABLED_VALUE) {
				$conditions[] = $validator['conditions'];
			}
		}

		if (!empty($conditions)) {
			$options['data-custom-validator-disable'] = json_encode($conditions);
		}

		return $options;
	}

	protected function _parseOptions(FieldDataEntity $Field, $options = []) {
		$data = $this->_View->get('customValidator');
		$options = parent::_parseOptions($Field, $options);

		$options = am($options, $this->getCustomOptions($Field));

		return $options;
	}

}