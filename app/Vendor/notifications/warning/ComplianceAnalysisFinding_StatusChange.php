<?php
class ComplianceAnalysisFinding_StatusChange extends StatusChange_Base {
	public $filename = 'ComplianceAnalysisFinding_StatusChange.php';
	public $model = 'ComplianceAnalysisFinding';
	public $defaultTypeSettings = [
		'model' => 'ComplianceAnalysisFinding',
		'callback' => 'afterSave',
		'type' => 'StatusChange',
	];
}
