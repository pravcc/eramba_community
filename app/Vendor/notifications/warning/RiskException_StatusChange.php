<?php
class RiskException_StatusChange extends StatusChange_Base {
	public $filename = 'RiskException_StatusChange.php';
	public $model = 'RiskException';
	public $defaultTypeSettings = [
		'model' => 'RiskException',
		'callback' => 'afterSave',
		'type' => 'StatusChange',
	];
}
