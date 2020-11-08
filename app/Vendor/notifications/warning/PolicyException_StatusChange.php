<?php
class PolicyException_StatusChange extends StatusChange_Base {
	public $filename = 'PolicyException_StatusChange.php';
	public $model = 'PolicyException';
	public $defaultTypeSettings = [
		'model' => 'PolicyException',
		'callback' => 'afterSave',
		'type' => 'StatusChange',
	];
}
