<?php
class ComplianceFinding_Created extends NotificationsBase {
	public $filename = 'ComplianceFinding_Created.php';
	public $internal = 'compliance_finding_audit_finding_created_trigger';
	public $model = 'ComplianceFinding';
	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'ComplianceFinding',
		'callback' => 'afterSave',
		'type' => 'AuditFindingCreated',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Audit Finding Item Created');
		$this->description = __('Notifies when an Audit Finding item is created');

		/*$this->conditions = array(
			$this->model . '.result' => AUDIT_PASSED
		);*/
	}
}
