<?php
App::uses('Component', 'Controller');
App::uses('ErambaCakeEmail', 'Network/Email');

class ComplianceAuditsComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

	public function afterItemSave($model, $data = null) {
		if (empty($data)) {
			$data = $this->controller->request->data;
		}
		
		if ($data[$model]['model'] != 'ComplianceAuditSetting') {
			return true;
		}

		$this->controller->{$model}->bindModel(array(
			'belongsTo' => array(
				'ComplianceAuditSetting' => array(
					'className' => 'ComplianceAuditSetting',
					'foreignKey' => 'foreign_key',
					'conditions' => array(
						$model . '.model' => 'ComplianceAuditSetting'
					)
				)
			)
		));

		if ($model == 'Comment') {
			$action = __('New Audit Feedback Comment');
		}
		elseif ($model == 'Attachment') {
			$action = __('New Audit Feedback Attachment');
		}
		else {
			$action = __('New Audit Feedback Provided');
		}


		$settings = $this->controller->{$model}->ComplianceAuditSetting->readSettings(null, null, $data[$model]['foreign_key'], true);

		$emailData = array(
			'model' => $model,
			'title' => $settings['CompliancePackageItem']['name'],
			'audit' => $settings['ComplianceAudit']['name'],
			'url' => array('plugin' => null, 'controller' => 'complianceAudits', 'action' => 'index'),
			'subject' => $action
		);

		$emailData['model'] = $model;
		$emailData['title'] = $settings['CompliancePackageItem']['name'];

		$notificationData = array(
			'model' => 'ComplianceAuditSetting',
			'url' => Router::url($emailData['url'])
		);
		$notificationData['title'] = $action;

		$this->controller->loadModel('Notification');
		$ret = true;
		$send = true;

		//if auditor
		if ($settings['ComplianceAudit']['auditor_id'] == $this->controller->logged['id']) {
			if ($settings['ComplianceAudit']['auditee_emails']) {

				$emails = array();
				foreach ($settings['Auditee'] as $auditee) {
					$emails[] = $auditee['email'];
				}

				$ret &= $send &= $this->sendAuditWarningEmails($emails, $emailData);
			}
			if ($settings['ComplianceAudit']['auditee_notifications']) {

				foreach ($settings['ComplianceAuditSetting']['auditee_id'] as $userId) {
					$notificationData['user_id'] = $userId;

					$ret &= $this->controller->Notification->setNotification($notificationData);
				}
			}
		}
		//if auditee
		elseif (in_array($this->controller->logged['id'], $settings['ComplianceAuditSetting']['auditee_id'])) {
			$auditorId = $settings['ComplianceAudit']['auditor_id'];
			if ($settings['ComplianceAudit']['auditor_emails']) {

				$ret &= $send &= $this->sendAuditWarningEmails($settings['ComplianceAudit']['Auditor']['email'], $emailData);
			}
			if ($settings['ComplianceAudit']['auditor_notifications']) {

				$notificationData['user_id'] = $auditorId;
				$ret &= $this->controller->Notification->setNotification($notificationData);
			}
		}

		if (!$send) {
			$this->errors[] = 'email';
		}

		//save system record of this action
		$this->controller->{$model}->ComplianceAuditSetting->addNoteToLog($action);
		$ret &= $this->controller->{$model}->ComplianceAuditSetting->setSystemRecord($data[$model]['foreign_key'], 2);
		
		return $ret;
	}

	protected function sendAuditWarningEmails($emails = array(), $emailData) {
		if (empty($emails)) {
			return true;
		}

		$email = new ErambaCakeEmail();
		$email->to($emails);
		$email->subject($emailData['subject']);
		$email->template('compliance_audits/warning');

		$emailData['url'] = Router::url($emailData['url'], true);
		$email->viewVars($emailData);

		return (bool) $email->send();
	}

}
