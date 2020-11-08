<?php
App::uses('AppHelper', 'View/Helper');
App::uses('AdvancedFilter', 'Model');
App::uses('Router', 'Routing');

class AdvancedFilterValuesHelper extends AppHelper
{
	public $helpers = ['Html', 'Form', 'FieldData.FieldData', 'Limitless.Alerts'];

	public function limitField(FieldDataEntity $Field)
	{
		$out = $this->FieldData->input($Field);

		return $out;
	}

}