<?php
App::uses('SectionBaseHelper', 'View/Helper');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class CustomLabelsHelper extends SectionBaseHelper
{
	public $helpers = ['FieldData.FieldData', 'LimitlessTheme.Alerts'];
	public $settings = [];

	public function labelField(FieldDataEntity $Field)
	{
		$message = '';

		if (empty($this->_message)) {
			$this->_message = true;

			if (Configure::read('debug')) {
				$message .= $this->Alerts->danger(__('Debug mode is enabled. Eramba will display custom label with original label in brackets.'));
			}			
		}

		return $message . $this->FieldData->input($Field, [
			'placeholder' => __('Custom label is not set, eramba is using default label')
		]);
	}

	public function descriptionField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'label' => false,
			'placeholder' => __('Custom description is not set, eramba is using default description')
		]);
	}
}