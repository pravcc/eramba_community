<?php
/**
 * @deprecated Moved to Security Service Audit section.
 */
class SecurityService_AuditBegin extends NotificationsBase {
	public $internal = 'security_service_audit_begin';
	public $model = 'SecurityService';

	public function __construct($options = array()) {
		parent::__construct($options);

		$this->deprecated = __('This notification was moved to Security Service Audit section.');

		$this->title = __('Security Control Audit About to Come');
		$this->description = __('Notifies 10 days before a scheduled Security Audit begins');

		$this->contain = array(
			'SecurityServiceAudit' => array(
				'conditions' => array(
					'SecurityServiceAudit.planned_date' => date('Y-m-d', strtotime('+10 days'))
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
