<?php
class AccountReviewFinding_StatusChange extends StatusChange_Base {
	public $filename = 'AccountReviewFinding_StatusChange.php';
	public $model = 'AccountReviewFinding';
	public $defaultTypeSettings = [
		'model' => 'AccountReviewFinding',
		'callback' => 'afterSave',
		'type' => 'StatusChange',
	];
}
