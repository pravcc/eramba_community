<?php
class ComplianceException_StatusChange extends StatusChange_Base {
	public $filename = 'ComplianceException_StatusChange.php';
	public $model = 'ComplianceException';
	public $defaultTypeSettings = [
		'model' => 'ComplianceException',
		'callback' => 'afterSave',
		'type' => 'StatusChange',
	];
}
