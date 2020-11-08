<?php
class SecurityServiceAudit_Passed extends NotificationsBase {
	public $filename = 'SecurityServiceAudit_Passed.php';
	public $internal = 'security_service_audit_passed_trigger';
	public $model = 'SecurityServiceAudit';
	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'SecurityServiceAudit',
		'callback' => 'afterSave',
		'type' => 'AuditPassed',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Security Service Audit Passed');
		$this->description = __('Notifies when the result of a security control audit is passed');

		$this->conditions = array(
			$this->model . '.result' => AUDIT_PASSED
		);
	}
}
