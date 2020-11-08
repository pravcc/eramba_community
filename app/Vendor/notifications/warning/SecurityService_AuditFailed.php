<?php
/**
 * @deprecated Moved to Security Service Audit section.
 */
class SecurityService_AuditFailed extends NotificationsBase {
	public $filename = 'SecurityService_AuditFailed.php';
	public $internal = 'security_service_audit_failed';
	public $model = 'SecurityService';
	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'SecurityService',
		'callback' => 'afterSave'
	);

	public function __construct($options = array()) {
		parent::__construct($options);

		$this->deprecated = __('This notification was moved to Security Service Audit section.');
		
		$this->title = __('Security Control Audit Failed');
		$this->description = __('Notifies when the resut of a security control audit is failed');

		$this->contain = array(
			'SecurityServiceAudit' => array(
				'conditions' => array(
					'result' => AUDIT_FAILED
				),
				'fields' => array('id', 'result', 'created')
			)
		);
	}

	public function parseData($item) {
		if (!empty($item['SecurityServiceAudit'])) {
			return true;
		}

		return false;
	}
}
