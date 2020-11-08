<?php
App::uses('FieldDataHelper', 'FieldData.View/Helper');
App::uses('CakeText', 'Utility');
App::uses('Hash', 'Utility');

class WorkflowConditionFieldsHelper extends FieldDataHelper {
	public $readonly = 0;
	public function comparisonInput($Field, FieldDataEntity $ValueField) {
		$options = [];
		
		if ((!$ValueField->isInteger() && !$ValueField->isFloat() && !$ValueField->isDate()) || $ValueField->isToggle()) {
			$options = [
				// 'disabled' => true,
				'readonly' => true,
				'data-readonly' => true,
				'default' => 1
			];
		}

		return $this->input($Field, $options);
	}

	protected function _parseOptions(FieldDataEntity $Field, $options = []) {
		$options = parent::_parseOptions($Field, $options);

		$options = Hash::merge($options, [
			'label' => ['class' => 'control-label'],
			'div' => 'col-md-3',
			'between' => null,
			'after' => CakeText::truncate($this->description($Field), 85)
		]);

		if ($Field->isToggle()) {
			$options['div'] = [
				'class' => 'col-md-3',
				'style' => 'margin-top:20px;'
			];
			$options['format'] = ['before', 'input', 'between', 'label', 'after', 'error'];
		}

		return $options;
	}

}