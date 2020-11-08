<?php
App::uses('ThirdPartyAuditsAppController', 'ThirdPartyAudits.Controller');
App::uses('Hash', 'Utility');
App::uses('ErambaCakeEmail', 'Network/Email');

class ThirdPartyAuditsController extends ThirdPartyAuditsAppController {
	public $uses = ['ComplianceAudit'];
	public $components = [
		'ComplianceAudits',
		'Ajax' => [
			'actions' => ['auditeeFeedback'],
			'modules' => ['comments', 'records', 'attachments', 'notifications']
		],
		'Pdf'
	];
	public $helpers = ['ComplianceAudits'];

	public function beforeFilter() {
		$this->Auth->allow('auditeeFeedbackStats');

		parent::beforeFilter();
	}

	protected function _setLayoutVars(CakeEvent $event) {
		parent::_setLayoutVars($event);

		$this->set('layout_headerPath', 'portal/header');
	}

	public function login() {
		$this->Portal->login([
			'loginTitle' => __('Compliance Audits Portal')
		]);
	}

	public function logout() {
		return $this->Portal->logout();
	}

	// read audit data for auditees to analyze
	protected function _readAnalyzeAudits($auditId = null) {
		$conds = [
			'ComplianceAuditSettingsAuditee.auditee_id' => $this->logged['id'],
			'ComplianceAudit.status' => COMPLIANCE_AUDIT_STARTED
		];

		if ($auditId !== null) {
			$conds['ComplianceAudit.id'] = $auditId;
		}

		return $this->ComplianceAudit->find('all', array(
			'conditions' => $conds,
			'group' => ['ComplianceAudit.id'],
			'fields' => array('id', 'name'),
			'joins' => [
				[
					'table' => 'compliance_audit_settings',
					'alias' => 'ComplianceAuditSetting',
					'type' => 'LEFT',
					'conditions' => [
						'ComplianceAuditSetting.compliance_audit_id = ComplianceAudit.id'
					]
				],
				[
					'table' => 'compliance_audit_settings_auditees',
					'alias' => 'ComplianceAuditSettingsAuditee',
					'type' => 'LEFT',
					'conditions' => [
						'ComplianceAuditSettingsAuditee.compliance_audit_setting_id = ComplianceAuditSetting.id'
					]
				]
			]
		));
	}

	public function index() {
		$this->set('title_for_layout', __('Third Party Audits'));
		$this->set('subtitle_for_layout', __('List of all audits waiting for your feedback'));

		$data = $this->_readAnalyzeAudits();

		$this->set('data', $data);
	}

	public function analyze($auditId = null, $successFeedback = false) {
		if (empty($auditId)) {
			throw new NotFoundException();
		}

		if (!$this->_readAnalyzeAudits($auditId)) {
			$this->Flash->error(__('Audit you tried to access does not need feedback from you or it is already completed.'));
			return $this->redirect(['action' => 'index']);
		}

		if ($this->request->is('post') && $this->provideAllFeedbacks($auditId)) {
			$successFeedback = true;
		}

		$this->loadModel('ComplianceAuditSettingsAuditee');
		$conds = array(
			'ComplianceAuditSettingsAuditee.auditee_id' => $this->logged['id']
		);
		if (!empty($auditId)) {
			$conds['ComplianceAuditSetting.compliance_audit_id'] = $auditId;
		}

		$data = $this->ComplianceAuditSettingsAuditee->find('all', array(
			'conditions' => $conds,
			'contain' => array(
				'ComplianceAuditSetting' => array(
					'ComplianceAudit',
					'ComplianceAuditAuditeeFeedback' => array(
						'User'
						// 'conditions' => array(
						// 	'user_id' => $this->logged['id']
						// ),
						// 'fields' => array('id')
					),
					'ComplianceAuditFeedbackProfile' => array(
						'ComplianceAuditFeedback'
					),
					'Comment',
					'Attachment',
					'CompliancePackageItem' => array(
						'CompliancePackage'
					),
				),
			)
		));

		$data = Hash::sort($data, '{n}.ComplianceAuditSetting.CompliancePackageItem.item_id', 'ASC');

		// debug($data);

		$this->loadModel('ComplianceAuditFeedback');
		$feedbacksData = $this->ComplianceAuditFeedback->find('all', array(
			'recursive' => -1
		));
		// debug($feedbacksData);
		$tmpData = $this->ComplianceAudit->find('first', array(
			'conditions' => array(
				'ComplianceAudit.id' => $auditId
			),
			'fields' => array('third_party_id', 'auditee_instructions', 'auditee_title', 'show_findings', 'status', 'name'),
			'recursive' => -1
		));

		$thirdParty = $this->ComplianceAudit->ThirdParty->find('first', array(
			'conditions' => array(
				'ThirdParty.id' => $tmpData['ComplianceAudit']['third_party_id']
			),
			'fields' => array('ThirdParty.id', 'ThirdParty.name'),
			'recursive' => -1
		));

		//count related findings
		$findings = $this->ComplianceAudit->ComplianceFinding->find('all', array(
			'conditions' => array(
				'ComplianceAuditSetting.compliance_audit_id' => $auditId
			),
			'fields' => array(
				'COUNT(ComplianceFinding.id) AS compliance_finding_count',
				'ComplianceAuditSetting.id'
			),
			'group' => array('ComplianceAuditSetting.id'),
			'joins' => array(
				array(
					'alias' => 'ComplianceAuditSetting',
					'table' => 'compliance_audit_settings',
					'type' => 'LEFT',
					'conditions' => array(
						'ComplianceAuditSetting.compliance_audit_id = ComplianceFinding.compliance_audit_id',
						'ComplianceAuditSetting.compliance_package_item_id = ComplianceFinding.compliance_package_item_id'
					)
				)
			),
			'recursive' => -1
		));

		$formatFindingsCount = Hash::combine(
			$findings,
			'{n}.ComplianceAuditSetting.id',
			'{n}.0.compliance_finding_count'
		);
		
		$title_for_layout = false;
		if (!empty($tmpData['ComplianceAudit']['auditee_title'])) {
			$title_for_layout = $tmpData['ComplianceAudit']['auditee_title'];
		}

		$this->set('title_for_layout', $title_for_layout);
		$this->set('data', $data);
		$this->set('auditId', $auditId);
		$this->set('auditData', $tmpData);
		$this->set('auditeeInstructions', $tmpData['ComplianceAudit']['auditee_instructions']);
		$this->set('feedbacksData', $feedbacksData);
		$this->set('successFeedback', $successFeedback);
		$this->set('formatFindingsCount', $formatFindingsCount);
	}

	private function provideAllFeedbacks($auditId) {
		$auditId = (int) $auditId;
		$data = $this->ComplianceAudit->find('first', array(
			'conditions' => array(
				'ComplianceAudit.status !=' => COMPLIANCE_AUDIT_STOPPED,
				'ComplianceAudit.id' => $auditId
			),
			'contain' => array(
				'ComplianceAuditSetting' => array(
					'conditions' => array(
						'ComplianceAuditSetting.compliance_audit_feedback_profile_id IS NOT NULL'
					),
					'ComplianceAuditAuditeeFeedback',
					'ComplianceAuditFeedbackProfile',
					'Auditee' => array(
						'conditions' => array(
							'Auditee.id' => $this->logged['id']
						),
					)
				),
			)
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		$allAnswersProvided = true;
		$allAuditeeAnswersProvided = true;
		foreach ($data['ComplianceAuditSetting'] as $item) {
			if (empty($item['ComplianceAuditAuditeeFeedback'])) {
				$allAnswersProvided = false;
				if (!empty($item['ComplianceAuditAuditeeFeedback'])) {
					$allAuditeeAnswersProvided = false;
				}
				// return $this->redirect(array('controller' => 'complianceAudits', 'action' => 'analyzeAuditee', $auditId));
				// return false;
			}
		}

		if ($allAnswersProvided == false && $allAuditeeAnswersProvided == true) {
			$this->Session->setFlash(__('We are not able to submit this questionnaire until you have completed all questions. Please review the yellow box next to submit to review what is missing.'), FLASH_ERROR);
			return false;
		}

		if ($allAnswersProvided == false) {
			$this->Session->setFlash(__('One or more items are missing feedback. Please provide feedback for all items and try again.'), FLASH_ERROR);
			return false;
		}

		$this->loadModel('ComplianceAuditProvidedFeedback');
		$this->ComplianceAuditProvidedFeedback->create();
		$ret = $this->ComplianceAuditProvidedFeedback->save(array(
			'user_id' => $this->logged['id'],
			'compliance_audit_id' => $auditId
		));

		$ret &= $this->ComplianceAudit->updateAll(array('ComplianceAudit.status' => '"' . COMPLIANCE_AUDIT_STOPPED . '"'), array('ComplianceAudit.id' => $auditId));

		if ($ret) {
			$this->notifyAuditor($auditId);

			$this->Session->setFlash(__('Thank you for your responses, the questionnaire has been completed and submitted to its owner.', $data['ComplianceAudit']['name']), FLASH_OK);
		}
		else {
			$this->Session->setFlash(__('Error occured while saving data. Please try again.'), FLASH_ERROR);
		}

		return true;
		// return $this->redirect(array('controller' => 'complianceAudits', 'action' => 'analyzeAuditee', $auditId, true));
	}

	/**
	 * Notify auditor via email that an Audit has been successfully analyzed.
	 */
	private function notifyAuditor($auditId) {
		$audit = $this->ComplianceAudit->find('first', array(
			'conditions' => array(
				'ComplianceAudit.id' => $auditId
			),
			'fields' => array(
				'ComplianceAudit.id',
				'ComplianceAudit.name',
				'Auditor.email'
			),
			'recursive' => 0
		));

		$auditTitle = $audit['ComplianceAudit']['name'];
		$auditorEmail = $audit['Auditor']['email'];

		$emailData = array();
		$emailData['audit'] = $auditTitle;

		$emailInstance = new ErambaCakeEmail('default');
		$emailInstance->to($auditorEmail);
		$emailInstance->subject(__('Third Party Audit Completed'));
		$emailInstance->template('compliance_audits/audit_analyzed');
		$emailInstance->viewVars($emailData);
				
		return $emailInstance->send();
	}

	public function auditeeFeedbackStats($auditId = null) {
		if (empty($auditId)) {
			throw new NotFoundException();
		}

		$data = $this->ComplianceAudit->find('first', array(
			'conditions' => array(
				'ComplianceAudit.id' => $auditId
			),
			'contain' => array(
				'ComplianceAuditSetting' => array(
					'conditions' => array(
						'ComplianceAuditSetting.compliance_audit_feedback_profile_id IS NOT NULL'
					),
					'ComplianceAuditAuditeeFeedback',
					'Auditee'
				),
			)
		));

		$this->set('complianceAudit', $data);

		$this->render('ThirdPartyAudits.../Elements/auditee_feedback_stats');
	}

	public function auditeeFeedback($auditSettingId) {
		$this->set('title_for_layout', __('Audit Feedback'));
		if (!$this->request->is('ajax')) {
			exit;
		}

		$error = false;
		$success = false;

		$this->loadModel('ComplianceAuditSettingsAuditee');
		$conds = array(
			'ComplianceAuditSettingsAuditee.auditee_id' => $this->logged['id'],
			'ComplianceAuditSetting.id' => $auditSettingId
		);
		$data = $this->ComplianceAuditSettingsAuditee->find('first', array(
			'conditions' => $conds,
			'contain' => array(
				'ComplianceAuditSetting' => array(
					'ComplianceAuditAuditeeFeedback' => array(
						'User'
					),
					'ComplianceAuditFeedbackProfile' => array(
						'ComplianceAuditFeedback'
					)
				),
			)
		));

		$setting = $data['ComplianceAuditSetting'];
		$auditId = $data['ComplianceAuditSetting']['compliance_audit_id'];
		$profile = $setting['ComplianceAuditFeedbackProfile'];

		if (empty($data)) {
			$error = __('You are not allowed to answer this question.');
		}

		$choices = array();
		if (!empty($profile['ComplianceAuditFeedback'])) {
			foreach ($profile['ComplianceAuditFeedback'] as $choice) {
				$choices[$choice['id']] = $choice['name'];
			}
		}

		$this->loadModel('ComplianceAuditAuditeeFeedback');

		if ($this->request->is('post') || $this->request->is('put')) {

			if (!empty($this->request->data['ComplianceAuditAuditeeFeedback']['choice_id'])) {
				$_userChoices = $this->request->data['ComplianceAuditAuditeeFeedback']['choice_id'];
				$_userChoices = [$_userChoices];

				$this->ComplianceAuditAuditeeFeedback->deleteAll(array(
					'ComplianceAuditAuditeeFeedback.compliance_audit_setting_id' => $auditSettingId,
					'ComplianceAuditAuditeeFeedback.compliance_audit_feedback_profile_id' => $profile['id']
				));

				$ret = true;
				$_choiceNames = array();
				foreach ($_userChoices as $_choice) {
					$this->ComplianceAuditAuditeeFeedback->create();
					$ret &= $this->ComplianceAuditAuditeeFeedback->save(array(
						'user_id' => $this->logged['id'],
						'compliance_audit_setting_id' => $auditSettingId,
						'compliance_audit_feedback_profile_id' => $profile['id'],
						'compliance_audit_feedback_id' => $_choice
					));

					$_choiceNames[] = $choices[$_choice];
				}

				$complianceAudit = array(
					'ComplianceAudit' => array(
						'model' => 'ComplianceAuditSetting',
						'foreign_key' => $auditSettingId
					)
				);

				if ($ret) {
					$log = __('User "%s" submitted a feedback profile "%s" and chose: %s',
						$this->logged['login'],
						$profile['name'],
						implode(', ', $_choiceNames));

					$this->ComplianceAudit->id = $auditId;
					$this->ComplianceAudit->addNoteToLog($log);
					$this->ComplianceAudit->setSystemRecord($auditId, 2);

					$this->ComplianceAudit->ComplianceAuditSetting->id = $auditSettingId;
					$this->ComplianceAudit->ComplianceAuditSetting->addNoteToLog($log);
					$this->ComplianceAudit->ComplianceAuditSetting->setSystemRecord($auditSettingId, 2);

					$success = __('Response saved - at all times you can close this window and continue later.');
				}
				else {
					$error = __('Error occured while saving your feedback. Please try again.');
				}
			}
			else {
				$error = __('Your response is required.');
			}
		}

		$this->set('setting', $data);
		$this->set('error', $error);
		$this->set('success', $success);
		$this->set('emptyAjaxLayout', true);
		$this->render('ThirdPartyAudits.../Elements/auditee_feedback');
	}

	/**
	 * Exports multiple Compliance Finding objects to a single PDF file keeping the original view.
	 */
	public function auditeeExportFindings($compliaceAuditSettingId = null) {
		$items = $this->ComplianceAudit->ComplianceFinding->find('all', array(
			'conditions' => array(
				'ComplianceAuditSetting.id' => $compliaceAuditSettingId
			),
			'joins' => array(
				array(
					'alias' => 'ComplianceAuditSetting',
					'table' => 'compliance_audit_settings',
					'type' => 'LEFT',
					'conditions' => array(
						'ComplianceAuditSetting.compliance_audit_id = ComplianceFinding.compliance_audit_id',
						'ComplianceAuditSetting.compliance_package_item_id = ComplianceFinding.compliance_package_item_id'
					)
				)
			)
		));

		$this->Pdf->renderPdfGroup(array(
			'name' => 'findings-review',
			'template' => '..'.DS.'ComplianceFindings'.DS.'export_pdf',
			'items' => $items
		));
	}
}