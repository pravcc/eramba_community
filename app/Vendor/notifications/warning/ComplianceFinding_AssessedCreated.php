<?php
class ComplianceFinding_AssessedCreated extends NotificationsBase {
	public $filename = 'ComplianceFinding_AssessedCreated.php';
	public $internal = 'compliance_finding_audit_assessed_created_trigger';
	public $model = 'ComplianceFinding';
	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'ComplianceFinding',
		'callback' => 'afterSave',
		'type' => 'AuditAssessedCreated',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Audit Assessed Item Created');
		$this->description = __('Notifies when an Audit Assessed item is created');

		/*$this->conditions = array(
			$this->model . '.result' => AUDIT_PASSED
		);*/
	}
}
