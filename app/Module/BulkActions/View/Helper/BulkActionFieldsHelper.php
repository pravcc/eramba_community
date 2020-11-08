<?php
App::uses('FieldDataHelper', 'FieldData.View/Helper');

class BulkActionFieldsHelper extends FieldDataHelper {
	public function getCustomOptions($Field) {
		return [
			'div' => null,
			'label' => $Field->getLabel(),
			'between' => null,
			'after' => null
		];
	}

	protected function _parseOptions(FieldDataEntity $Field, $options = []) {
		$options = parent::_parseOptions($Field, $options);

		$options = am($options, $this->getCustomOptions($Field));

		return $options;
	}

}