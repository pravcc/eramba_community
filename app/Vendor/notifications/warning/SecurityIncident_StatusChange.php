<?php
class SecurityIncident_StatusChange extends StatusChange_Base {
	public $filename = 'SecurityIncident_StatusChange.php';
	public $model = 'SecurityIncident';
	public $defaultTypeSettings = [
		'model' => 'SecurityIncident',
		'callback' => 'afterSave',
		'type' => 'StatusChange',
	];
}
