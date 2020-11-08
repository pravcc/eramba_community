<?php
class SecurityServiceAudit_Failed extends NotificationsBase {
	public $filename = 'SecurityServiceAudit_Failed.php';
	public $internal = 'security_service_audit_failed_trigger';
	public $model = 'SecurityServiceAudit';
	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'SecurityServiceAudit',
		'callback' => 'afterSave',
		'type' => 'AuditFailed',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Security Service Audit Failed');
		$this->description = __('Notifies when the result of a security control audit is failed');

		$this->conditions = array(
			$this->model . '.result' => AUDIT_FAILED
		);
	}
}
