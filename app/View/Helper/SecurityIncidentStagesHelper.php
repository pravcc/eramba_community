<?php
App::uses('AppHelper', 'View/Helper');
App::uses('CakeText', 'Utility');
App::uses('SecurityIncidentStagesSecurityIncident', 'Model');

class SecurityIncidentStagesHelper extends AppHelper
{
	public $helpers = ['Html', 'FieldData.FieldData', 'LimitlessTheme.Alerts'];

	public function statusField(FieldDataEntity $Field)
	{
		$out = $this->FieldData->input($Field);

		return $out;
	}
}