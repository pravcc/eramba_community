<?php
App::uses('CakeObject', 'Core');
App::uses('SystemShell', 'Console/Command');
App::uses('ReportsShell', 'Reports.Console/Command');
App::uses('CommentsModule', 'Comments.Lib');
App::uses('AttachmentsModule', 'Attachments.Lib');
App::uses('UserFields', 'UserFields.Lib');
App::uses('ObjectStatusModule', 'ObjectStatus.Lib');
App::uses('UserFieldsModule', 'UserFields.Lib');

/**
 * Library for new template's DB migration
 */
class NewTemplateDbMigration extends CakeObject {
	protected $_invertFilterValue = [
		'Risk' => [
			'audits_last_not_passed'
		],
		'ThirdPartyRisk' => [
			'audits_last_not_passed'
		],
		'BusinessContinuity' => [
			'audits_last_not_passed'
		],
		'ComplianceManagement' => [
			'security_service_audits_last_not_passed'
		]
	];

	protected $_statusLegacy = [
		'Risk' => [
			'security_incident_ongoing_incident' => 'ongoing_incident',
			'security_service_audits_last_passed' => 'audits_last_not_passed',
			'security_service_audits_last_missing' => 'audits_last_missing',
			'security_service_maintenances_last_missing' => 'maintenances_last_missing',
			'security_service_ongoing_corrective_actions' => 'ongoing_corrective_actions',
			'security_service_control_with_issues' => 'control_with_issues',
			'asset_expired_reviews' => false
		],
		'ThirdPartyRisk' => [
			'security_incident_ongoing_incident' => 'ongoing_incident',
			'security_service_audits_last_passed' => 'audits_last_not_passed',
			'security_service_audits_last_missing' => 'audits_last_missing',
			'security_service_maintenances_last_missing' => 'maintenances_last_missing',
			'security_service_ongoing_corrective_actions' => 'ongoing_corrective_actions',
			'security_service_control_with_issues' => 'control_with_issues',
			'asset_expired_reviews' => false
		],
		'BusinessContinuity' => [
			'security_incident_ongoing_incident' => 'ongoing_incident',
			'security_service_audits_last_passed' => 'audits_last_not_passed',
			'security_service_audits_last_missing' => 'audits_last_missing',
			'security_service_maintenances_last_missing' => 'maintenances_last_missing',
			'security_service_ongoing_corrective_actions' => 'ongoing_corrective_actions',
			'security_service_control_with_issues' => 'control_with_issues',
			'asset_expired_reviews' => false
		],
		'ComplianceManagement' => [
			'security_service_audits_last_passed' => 'security_service_audits_last_not_passed',
			'project_expired_tasks' => 'project_expired_task'
		],
		'Asset' => [
			'security_incident_ongoing_incident' => 'ongoing_incident'
		],
		'SecurityService' => [
			'security_incident_ongoing_incident' => 'ongoing_incident'
		]
	];

	public function run()
	{
		if (AppModule::loaded('NotificationSystem')) {
			// temporary class load in case it loads the old one
			ClassRegistry::init('NotificationSystem.NotificationSystem');
		}

		$ret = true;

		$ret &= $this->_policyUpdates();
		
		if (AppModule::loaded('NotificationSystem')) {
			$this->_notificationUserFields();

			$ret &= $this->_notificationFileNames();
		}

		// $ret &= self::additionalDbSync();

		$ret &= $this->_allButSettingsAcl();

		$ret &= $this->_filterParams();

		return $ret;
	}

	// set review document types
	protected function _policyUpdates()
	{
		$ret = true;

		$SecurityPolicy = ClassRegistry::init('SecurityPolicy');
		$SecurityPolicyReview = ClassRegistry::init('SecurityPolicyReview');

		$data = $SecurityPolicy->find('all', [
			'recursive' => -1
		]);

		foreach ($data as $item) {
			$review = $SecurityPolicyReview->find('first', [
				'conditions' => [
					'SecurityPolicyReview.foreign_key' => $item['SecurityPolicy']['id'],
					'SecurityPolicyReview.model' => 'SecurityPolicy',
					'SecurityPolicyReview.completed' => 1,
					'SecurityPolicyReview.planned_date <' => $item['SecurityPolicy']['next_review_date']
				],
				'order' => [
					'SecurityPolicyReview.planned_date' => 'DESC'
				],
				'recursive' => -1
			]);

			if (!empty($review)) {
				if ($item['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_URL) {
					$ret &= (bool) $SecurityPolicyReview->updateAll([
						'SecurityPolicyReview.use_attachments' => $item['SecurityPolicy']['use_attachments'],
						'SecurityPolicyReview.url' => "'" . $item['SecurityPolicy']['url'] . "'"
					], [
						'SecurityPolicyReview.id' => $review['SecurityPolicyReview']['id']
					]);
				}

				$ds = $SecurityPolicyReview->getDataSource();
				if ($item['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_CONTENT) {
				    $ret &= (bool) $SecurityPolicyReview->updateAll([
				        'SecurityPolicyReview.use_attachments' => $item['SecurityPolicy']['use_attachments'],
				        'SecurityPolicyReview.policy_description' => $ds->value($item['SecurityPolicy']['description'])
				    ], [
				        'SecurityPolicyReview.id' => $review['SecurityPolicyReview']['id']
				    ]);
				}

				if ($item['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_ATTACHMENTS) {
					$ret &= (bool) $SecurityPolicyReview->updateAll([
						'SecurityPolicyReview.use_attachments' => $item['SecurityPolicy']['use_attachments']
					], [
						'SecurityPolicyReview.id' => $review['SecurityPolicyReview']['id']
					]);
				}
			}
		}

		return $ret;
	}

	protected function _notificationFileNames()
	{
		$update = [
			'AdvancedFilterResult.php' => 'advanced_filters',
			'Attachment_added.php' => 'attachments',
			'Comment_Added.php' => 'comments',
			'_ObjectReminder.php' => 'object_reminder',

			// assets
			'Asset_Expiration_009.php' => 'asset_expiration_-30day',
			'Asset_Expiration_008.php' => 'asset_expiration_-20day',
			'Asset_Expiration.php' => 'asset_expiration_-10day',
			'Asset_Expiration_001.php' => 'asset_expiration_-5day',
			'Asset_Expiration_002.php' => 'asset_expiration_-1day',
			'Asset_Expiration_003.php' => 'asset_expiration_+1day',
			'Asset_Expiration_004.php' => 'asset_expiration_+5day',
			'Asset_Expiration_005.php' => 'asset_expiration_+10day',
			'Asset_Expiration_006.php' => 'asset_expiration_+20day',
			'Asset_Expiration_007.php' => 'asset_expiration_+30day',

			// security service audits
			'SecurityServiceAudit_Begin_010.php' => 'security_service_audit_begin_-30day',
			'SecurityServiceAudit_Begin_009.php' => 'security_service_audit_begin_-20day',
			'SecurityServiceAudit_Begin_001.php' => 'security_service_audit_begin_-10day',
			'SecurityServiceAudit_Begin.php' => 'security_service_audit_begin_-10day',
			'SecurityServiceAudit_Begin_002.php' => 'security_service_audit_begin_-5day',
			'SecurityServiceAudit_Begin_003.php' => 'security_service_audit_begin_-1day',
			'SecurityServiceAudit_Begin_004.php' => 'security_service_audit_begin_+1day',
			'SecurityServiceAudit_Begin_005.php' => 'security_service_audit_begin_+5day',
			'SecurityServiceAudit_Begin_006.php' => 'security_service_audit_begin_+10day',
			'SecurityServiceAudit_Begin_007.php' => 'security_service_audit_begin_+20day',
			'SecurityServiceAudit_Begin_008.php' => 'security_service_audit_begin_+30day',
			'SecurityServiceAudit_Failed.php' => 'security_service_audit_failed',
			'SecurityServiceAudit_Passed.php' => 'security_service_audit_passed',

			// security service maintenances
			'SecurityServiceMaintenance_Start_008.php' => 'security_service_maintenance_begin_-30day',
			'SecurityServiceMaintenance_Start_007.php' => 'security_service_maintenance_begin_-20day',
			'SecurityServiceMaintenance_Start_000.php' => 'security_service_maintenance_begin_-10day',
			'SecurityServiceMaintenance_Start.php' => 'security_service_maintenance_begin_-5day',
			'SecurityServiceMaintenance_Start_001.php' => 'security_service_maintenance_begin_-1day',
			'SecurityServiceMaintenance_Start_002.php' => 'security_service_maintenance_begin_+1day',
			'SecurityServiceMaintenance_Start_003.php' => 'security_service_maintenance_begin_+5day',
			'SecurityServiceMaintenance_Start_004.php' => 'security_service_maintenance_begin_+10day',
			'SecurityServiceMaintenance_Start_005.php' => 'security_service_maintenance_begin_+20day',
			'SecurityServiceMaintenance_Start_006.php' => 'security_service_maintenance_begin_+30day',

			// service contracts
			'ServiceContract_Expiration_000.php' => 'service_contract_expiration_-30day',
			'ServiceContract_Expiration_008.php' => 'service_contract_expiration_-20day',
			'ServiceContract_Expiration.php' => 'service_contract_expiration_-10day',
			'ServiceContract_Expiration_001.php' => 'service_contract_expiration_-5day',
			'ServiceContract_Expiration_002.php' => 'service_contract_expiration_-1day',
			'ServiceContract_Expiration_003.php' => 'service_contract_expiration_+1day',
			'ServiceContract_Expiration_004.php' => 'service_contract_expiration_+5day',
			'ServiceContract_Expiration_005.php' => 'service_contract_expiration_+10day',
			'ServiceContract_Expiration_006.php' => 'service_contract_expiration_+20day',
			'ServiceContract_Expiration_007.php' => 'service_contract_expiration_+30day',

			// service contracts
			'SecurityPolicy_ReviewDate_009.php' => 'security_policy_review_-30day',
			'SecurityPolicy_ReviewDate_008.php' => 'security_policy_review_-20day',
			'SecurityPolicy_ReviewDate.php' => 'security_policy_review_-10day',
			'SecurityPolicy_ReviewDate_001.php' => 'security_policy_review_-5day',
			'SecurityPolicy_ReviewDate_002.php' => 'security_policy_review_-1day',
			'SecurityPolicy_ReviewDate_003.php' => 'security_policy_review_+1day',
			'SecurityPolicy_ReviewDate_004.php' => 'security_policy_review_+5day',
			'SecurityPolicy_ReviewDate_005.php' => 'security_policy_review_+10day',
			'SecurityPolicy_ReviewDate_006.php' => 'security_policy_review_+20day',
			'SecurityPolicy_ReviewDate_007.php' => 'security_policy_review_+30day',

			// policy exceptions
			'PolicyException_StatusChange.php' => 'status_change',
			'PolicyException_Expiration_009.php' => 'policy_exception_expiration_-30day',
			'PolicyException_Expiration_008.php' => 'policy_exception_expiration_-20day',
			'PolicyException_Expiration.php' => 'policy_exception_expiration_-10day',
			'PolicyException_Expiration_001.php' => 'policy_exception_expiration_-5day',
			'PolicyException_Expiration_002.php' => 'policy_exception_expiration_-1day',
			'PolicyException_Expiration_003.php' => 'policy_exception_expiration_+1day',
			'PolicyException_Expiration_004.php' => 'policy_exception_expiration_+5day',
			'PolicyException_Expiration_005.php' => 'policy_exception_expiration_+10day',
			'PolicyException_Expiration_006.php' => 'policy_exception_expiration_+20day',
			'PolicyException_Expiration_007.php' => 'policy_exception_expiration_+30day',

			// risk exceptions
			'RiskException_StatusChange.php' => 'status_change',
			'RiskException_Expiration_009.php' => 'risk_exception_expiration_-30day',
			'RiskException_Expiration_008.php' => 'risk_exception_expiration_-20day',
			'RiskException_Expiration.php' => 'risk_exception_expiration_-10day',
			'RiskException_Expiration_001.php' => 'risk_exception_expiration_-5day',
			'RiskException_Expiration_002.php' => 'risk_exception_expiration_-1day',
			'RiskException_Expiration_003.php' => 'risk_exception_expiration_+1day',
			'RiskException_Expiration_004.php' => 'risk_exception_expiration_+5day',
			'RiskException_Expiration_005.php' => 'risk_exception_expiration_+10day',
			'RiskException_Expiration_006.php' => 'risk_exception_expiration_+20day',
			'RiskException_Expiration_007.php' => 'risk_exception_expiration_+30day',

			// compliance exceptions
			'ComplianceException_StatusChange.php' => 'status_change',
			'ComplianceException_Expiration_009.php' => 'compliance_exception_expiration_-30day',
			'ComplianceException_Expiration_008.php' => 'compliance_exception_expiration_-20day',
			'ComplianceException_Expiration.php' => 'compliance_exception_expiration_-10day',
			'ComplianceException_Expiration_001.php' => 'compliance_exception_expiration_-5day',
			'ComplianceException_Expiration_002.php' => 'compliance_exception_expiration_-1day',
			'ComplianceException_Expiration_003.php' => 'compliance_exception_expiration_+1day',
			'ComplianceException_Expiration_004.php' => 'compliance_exception_expiration_+5day',
			'ComplianceException_Expiration_005.php' => 'compliance_exception_expiration_+10day',
			'ComplianceException_Expiration_006.php' => 'compliance_exception_expiration_+20day',
			'ComplianceException_Expiration_007.php' => 'compliance_exception_expiration_+30day',

			// risks
			'Risk_Expiration_010.php' => 'risk_expiration_-30day',
			'Risk_Expiration_009.php' => 'risk_expiration_-20day',
			'Risk_Expiration.php' => 'risk_expiration_-10day',
			'Risk_Expiration_001.php' => 'risk_expiration_-5day',
			'Risk_Expiration_002.php' => 'risk_expiration_-1day',
			'Risk_Expiration_003.php' => 'risk_expiration_+1day',
			'Risk_Expiration_004.php' => 'risk_expiration_+5day',
			'Risk_Expiration_006.php' => 'risk_expiration_+10day',
			'Risk_Expiration_007.php' => 'risk_expiration_+20day',
			'Risk_Expiration_008.php' => 'risk_expiration_+30day',

			// third party risks
			'ThirdPartyRisk_Expiration_009.php' => 'third_party_risk_expiration_-30day',
			'ThirdPartyRisk_Expiration_008.php' => 'third_party_risk_expiration_-20day',
			'ThirdPartyRisk_Expiration.php' => 'third_party_risk_expiration_-10day',
			'ThirdPartyRisk_Expiration_001.php' => 'third_party_risk_expiration_-5day',
			'ThirdPartyRisk_Expiration_002.php' => 'third_party_risk_expiration_-1day',
			'ThirdPartyRisk_Expiration_003.php' => 'third_party_risk_expiration_+1day',
			'ThirdPartyRisk_Expiration_004.php' => 'third_party_risk_expiration_+5day',
			'ThirdPartyRisk_Expiration_005.php' => 'third_party_risk_expiration_+10day',
			'ThirdPartyRisk_Expiration_006.php' => 'third_party_risk_expiration_+20day',
			'ThirdPartyRisk_Expiration_007.php' => 'third_party_risk_expiration_+30day',

			// business risks
			'BusinessContinuity_Expiration_009.php' => 'business_continuity_expiration_-30day',
			'BusinessContinuity_Expiration_008.php' => 'business_continuity_expiration_-20day',
			'BusinessContinuity_Expiration.php' => 'business_continuity_expiration_-10day',
			'BusinessContinuity_Expiration_001.php' => 'business_continuity_expiration_-5day',
			'BusinessContinuity_Expiration_002.php' => 'business_continuity_expiration_-1day',
			'BusinessContinuity_Expiration_003.php' => 'business_continuity_expiration_+1day',
			'BusinessContinuity_Expiration_004.php' => 'business_continuity_expiration_+5day',
			'BusinessContinuity_Expiration_005.php' => 'business_continuity_expiration_+10day',
			'BusinessContinuity_Expiration_006.php' => 'business_continuity_expiration_+20day',
			'BusinessContinuity_Expiration_007.php' => 'business_continuity_expiration_+30day',

			// complianec analysis finding
			'ComplianceAnalysisFinding_Expiration_009.php' => 'compliance_analysis_finding_expiration_-30day',
			'ComplianceAnalysisFinding_Expiration_008.php' => 'compliance_analysis_finding_expiration_-20day',
			'ComplianceAnalysisFinding_Expiration.php' => 'compliance_analysis_finding_expiration_-10day',
			'ComplianceAnalysisFinding_Expiration_001.php' => 'compliance_analysis_finding_expiration_-5day',
			'ComplianceAnalysisFinding_Expiration_002.php' => 'compliance_analysis_finding_expiration_-1day',
			'ComplianceAnalysisFinding_Expiration_003.php' => 'compliance_analysis_finding_expiration_+1day',
			'ComplianceAnalysisFinding_Expiration_004.php' => 'compliance_analysis_finding_expiration_+5day',
			'ComplianceAnalysisFinding_Expiration_005.php' => 'compliance_analysis_finding_expiration_+10day',
			'ComplianceAnalysisFinding_Expiration_006.php' => 'compliance_analysis_finding_expiration_+20day',
			'ComplianceAnalysisFinding_Expiration_007.php' => 'compliance_analysis_finding_expiration_+30day',
			'ComplianceAnalysisFinding_StatusChange.php' => 'status_change',

			// pojects
			'Project_Deadline_010.php' => 'project_deadline_-30day',
			'Project_Deadline_009.php' => 'project_deadline_-20day',
			'Project_Deadline_001.php' => 'project_deadline_-10day',
			'Project_Deadline_002.php' => 'project_deadline_-5day',
			'Project_Deadline_003.php' => 'project_deadline_-1day',
			'Project_Deadline_004.php' => 'project_deadline_+1day',
			'Project_Deadline_005.php' => 'project_deadline_+5day',
			'Project_Deadline_006.php' => 'project_deadline_+10day',
			'Project_Deadline_007.php' => 'project_deadline_+20day',
			'Project_Deadline_008.php' => 'project_deadline_+30day',
			'Project_NoActivity.php' => 'project_no_activity_+5day',
			'Project_NoActivity_001.php' => 'project_no_activity_+10day',
			'Project_NoActivity_002.php' => 'project_no_activity_+15day',
			'Project_NoActivity_003.php' => 'project_no_activity_+20day',
			'Project_NoActivity_004.php' => 'project_no_activity_+30day',

			// project achievement
			'ProjectAchievement_Deadline_010.php' => 'project_achievement_deadline_-30day',
			'ProjectAchievement_Deadline_009.php' => 'project_achievement_deadline_-20day',
			'ProjectAchievement_Deadline_001.php' => 'project_achievement_deadline_-10day',
			'ProjectAchievement_Deadline_002.php' => 'project_achievement_deadline_-5day',
			'ProjectAchievement_Deadline_003.php' => 'project_achievement_deadline_-1day',
			'ProjectAchievement_Deadline_004.php' => 'project_achievement_deadline_+1day',
			'ProjectAchievement_Deadline_005.php' => 'project_achievement_deadline_+5day',
			'ProjectAchievement_Deadline_006.php' => 'project_achievement_deadline_+10day',
			'ProjectAchievement_Deadline_007.php' => 'project_achievement_deadline_+20day',
			'ProjectAchievement_Deadline_008.php' => 'project_achievement_deadline_+30day',
			'ProjectAchievement_NoActivity.php' => 'project_achievement_no_activity_+5day',
			'ProjectAchievement_NoActivity_001.php' => 'project_achievement_no_activity_+10day',
			'ProjectAchievement_NoActivity_002.php' => 'project_achievement_no_activity_+15day',
			'ProjectAchievement_NoActivity_003.php' => 'project_achievement_no_activity_+20day',
			'ProjectAchievement_NoActivity_004.php' => 'project_achievement_no_activity_+30day',

			// security incident
			'SecurityIncident_StatusChange.php' => 'status_change',

			// vendor assessments
			'VendorAssessment_End.php' => 'vendor_assessment_end',
			'VendorAssessment_EndDate_005.php' => 'vendor_assessment_end_date_-30days',
			'VendorAssessment_EndDate_004.php' => 'vendor_assessment_end_date_-20days',
			'VendorAssessment_EndDate.php' => 'vendor_assessment_end_date_-15days',
			'VendorAssessment_EndDate_001.php' => 'vendor_assessment_end_date_-10days',
			'VendorAssessment_EndDate_002.php' => 'vendor_assessment_end_date_-5days',
			'VendorAssessment_EndDate_003.php' => 'vendor_assessment_end_date_-1day',
			'VendorAssessment_FirstActivity.php' => 'vendor_assessment_first_activity',
			'VendorAssessment_QuestionAnswer.php' => 'vendor_assessment_question_answer',
			'VendorAssessment_RecurrenceDate_004.php' => 'vendor_assessment_recurrence_date_-30days',
			'VendorAssessment_RecurrenceDate_003.php' => 'vendor_assessment_recurrence_date_-20days',
			'VendorAssessment_RecurrenceDate.php' => 'vendor_assessment_recurrence_date_-10days',
			'VendorAssessment_RecurrenceDate_001.php' => 'vendor_assessment_recurrence_date_-5days',
			'VendorAssessment_RecurrenceDate_002.php' => 'vendor_assessment_recurrence_date_-1day',
			'VendorAssessment_Start.php' => 'vendor_assessment_start',
			'VendorAssessment_Submit.php' => 'vendor_assessment_submit',

			// vendor assessment findings
			'VendorAssessmentFinding_Deadline.php' => 'vendor_assessment_finding_deadline_-10days',
			'VendorAssessmentFinding_Deadline_001.php' => 'vendor_assessment_finding_deadline_-5days',
			'VendorAssessmentFinding_Deadline_002.php' => 'vendor_assessment_finding_deadline_-1day',
			'VendorAssessmentFinding_Deadline_003.php' => 'vendor_assessment_finding_deadline_-20days',
			'VendorAssessmentFinding_Deadline_004.php' => 'vendor_assessment_finding_deadline_-30days',

			// account review findings
			'AccountReviewFinding_Created.php' => 'account_review_finding_created',
			'AccountReviewFinding_Expiration.php' => 'account_review_finding_expiration_-30days',
			'AccountReviewFinding_Expiration_001.php' => 'account_review_finding_expiration_-20days',
			'AccountReviewFinding_Expiration_002.php' => 'account_review_finding_expiration_-10days',
			'AccountReviewFinding_Expiration_003.php' => 'account_review_finding_expiration_-5days',
			'AccountReviewFinding_Expiration_004.php' => 'account_review_finding_expiration_-1day',
			'AccountReviewFinding_Expiration_005.php' => 'account_review_finding_expiration_+1day',
			'AccountReviewFinding_Expiration_006.php' => 'account_review_finding_expiration_+5days',
			'AccountReviewFinding_Expiration_007.php' => 'account_review_finding_expiration_+10days',
			'AccountReviewFinding_Expiration_008.php' => 'account_review_finding_expiration_+20days',
			'AccountReviewFinding_Expiration_009.php' => 'account_review_finding_expiration_+30days',
			'AccountReviewFinding_StatusChange.php' => 'status_change',

			// account review pulls
			'AccountReviewPull_Differential.php' => 'account_review_pull_differential',
			'AccountReviewPull_Exits.php' => 'account_review_pull_exits',
			'AccountReviewPull_Fail.php' => 'account_review_pull_fail',
			'AccountReviewPull_Success.php' => 'account_review_pull_success',
			'AccountReviewPull_Submit.php' => 'account_review_pull_submit',
			'AccountReviewPull_SubmitNotOk.php' => 'account_review_pull_submit_not_ok',
			'AccountReviewPull_SubmitNotSure.php' => 'account_review_pull_submit_not_sure',
			'AccountReviewPull_Pending.php' => 'account_review_pull_pending_+1day',
			'AccountReviewPull_Pending_001.php' => 'account_review_pull_pending_+5days',
			'AccountReviewPull_Pending_002.php' => 'account_review_pull_pending_+10days',
			'AccountReviewPull_Pending_003.php' => 'account_review_pull_pending_+20days',
			'AccountReviewPull_Pending_004.php' => 'account_review_pull_pending_+30days',
		];

		// deprecated ones and old ones
		$delete = [
			'SecurityService_AuditBegin.php',
			'SecurityService_AuditFailed.php',
			'Project_Inactivity.php',
			'Project_ProjectTaskUpdates.php',
			'ProjectAchievement_Inactivity.php',

			//??
			'BusinessContinuityPlan_AuditBegin_009.php',
			'BusinessContinuityPlan_AuditBegin_008.php',
			'BusinessContinuityPlan_AuditBegin.php',
			'BusinessContinuityPlan_AuditBegin_001.php',
			'BusinessContinuityPlan_AuditBegin_002.php',
			'BusinessContinuityPlan_AuditBegin_003.php',
			'BusinessContinuityPlan_AuditBegin_004.php',
			'BusinessContinuityPlan_AuditBegin_005.php',
			'BusinessContinuityPlan_AuditBegin_006.php',
			'BusinessContinuityPlan_AuditBegin_007.php',
			'BusinessContinuityPlan_AuditFailed.php'
		];

		$ret = true;

		$NotificationSystem = ClassRegistry::init('NotificationSystem.NotificationSystem');
		foreach ($update as $prevName => $newName) {
			$fileData = $NotificationSystem->find('first', [
				'conditions' => [
					'NotificationSystem.filename' => $prevName
				],
				'fields' => [
					'NotificationSystem.model'
				],
				'recursive' => -1
			]);

			if (empty($fileData)) {
				continue;
			}

			$fileModel = $fileData['NotificationSystem']['model'];

			$fileModelInstance = ClassRegistry::init($fileModel);
			$fileModelInstance->Behaviors->load('NotificationSystem.NotificationSystem');
			$fileModelInstance->Behaviors->NotificationSystem->setup($fileModelInstance);

			$NotificationInstance = $fileModelInstance->initNotification($newName);
			$fileSaveData = $NotificationInstance->toSaveData();
			unset($fileSaveData['name']);

			$updateData = [
				'filename' => "'" . $newName . "'",
				'email_subject' => "'" . $fileSaveData['email_subject'] . "'",
				'email_body' => "'" . $fileSaveData['email_body'] . "'",
			];	

			$ret &= (bool) $NotificationSystem->updateAll([
				'filename' => "'" . $newName . "'"
			], [
				'NotificationSystem.filename' => $prevName,
				'NotificationSystem.email_customized' => 1
			]);

			$ret &= (bool) $NotificationSystem->updateAll($updateData, [
				'NotificationSystem.filename' => $prevName,
				'NotificationSystem.email_customized' => 0
			]);
		}

		foreach ($delete as $deleteName) {
			$ret &= (bool) $NotificationSystem->deleteAll([
				'NotificationSystem.filename' => $deleteName
			]);
		}

		return $ret;
	}

	public static function additionalDbSync()
	{
		$ret = true;

		// correct model names to have pluign prefixes
		$ret &= CommentsModule::syncFullModelName();
		$ret &= AttachmentsModule::syncFullModelName();

		// Sync Visualisations for new objects and sections
		$Setting = ClassRegistry::init('Setting');
		$ret &= $Setting->syncVisualisation();

        // sync object statuses
        $UserFieldsModule = new UserFieldsModule();
        $ret &= $UserFieldsModule->syncExistingObjects();

		// sync statuses
        $ObjectStatusModule = new ObjectStatusModule();
        $ret &= $ObjectStatusModule->syncAllStatuses();

		// Sync Advanced Filter's system filters
		$SystemShell = self::loadShell('SystemShell');
		$ret &= $SystemShell->sync_db();

		// Sync default reports
		$ReportsShell = self::loadShell('ReportsShell');
		$ret &= $ReportsShell->seed_reports();

		// add creation event versioning for new models using auditable behavior
        $ObjectVersion = ClassRegistry::init('ObjectVersion.ObjectVersion');
		$ret &= $ObjectVersion->addMissingVersioning();

		App::uses('AssociativeDeleteShell', 'AssociativeDelete.Console/Command');
        $AssociativeDeleteShell = new AssociativeDeleteShell();
        $AssociativeDeleteShell->startup();
        $ret &= $AssociativeDeleteShell->delete_remaining_compliance_items();

		return $ret;
	}

	protected function _notificationUserFields()
	{
		// Update notifications table data
        $UserFields = new UserFields();

        $fields = [
            'NotificationUser' => [
            	'NotificationUser' => [
	            	'joinTable' => 'notification_system_items_users',
	            	'foreignKey' => 'notification_system_item_id',
	            	'conditions' => [
	            		'type' => 0
	            	]
            	]
            ]
        ];

        $UserFields->moveExistingFieldsToUserFieldsTable('up', 'NotificationSystem', $fields);
	}

	// adjust advanced filter's filter fields to match new naming convention
	protected function _filterParams()
	{
		$ret = true;

		$AdvancedFilterValue = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');

		// first update legacy filter fields to the new names
		foreach ($this->_statusLegacy as $model => $replaceParts) {
			$Model = ClassRegistry::init($model);

			foreach ($replaceParts as $oldFieldName => $newFieldName) {
				// false value removes all filter field records from the database
				if ($newFieldName === false) {
					$ret &= $this->_removeFilterField($Model, $oldFieldName);
				} else {
					$ret &= $this->_updateFilterField($Model, $oldFieldName, $newFieldName);
					$ret &= $this->_updateFilterField($Model, $oldFieldName . '__show', $newFieldName . '__show');
					$ret &= $this->_updateFilterField($Model, $oldFieldName . '__comp_type', $newFieldName . '__comp_type');
				}
			}
		}

		// prefix 'customField__' changed to 'CustomFields_'
		$allowedIds = $AdvancedFilterValue->find('list', [
			'conditions' => [
				'AdvancedFilterValue.field LIKE' => 'customField__%'
			],
			'fields' => [
				'id'
			],
			'recursive' => -1
		]);

		$ret &= (bool) $AdvancedFilterValue->updateAll([
			'AdvancedFilterValue.field' => "REPLACE(field, 'customField__', 'CustomFields_')"
		], [
			'AdvancedFilterValue.id' => $allowedIds
		]);

		// update statuses filter fields that uses 'ObjectStatus_' prefix now
		// object status filter param prefix
		$modelList = ObjectStatusModule::modelNames();

		foreach ($modelList as $model) {
			$Model = ClassRegistry::init($model);
			if (!$Model->Behaviors->enabled('ObjectStatus.ObjectStatus')) {
				continue;
			}

			$statusFields = $Model->Behaviors->ObjectStatus->getObjectStatusFilterFields($Model);
			if (empty($statusFields)) {
				continue;
			}

			foreach ($statusFields as $field) {
				$ret &= $this->_updateStatusField($Model, $field);
				$ret &= $this->_updateStatusField($Model, $field . '__show');
				$ret &= $this->_updateStatusField($Model, $field . '__comp_type');
			}
		}

		// remove existing data asset filters as it cant be migrated
		$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		$ret &= (bool) $AdvancedFilter->deleteAll([
			'AdvancedFilter.model' => 'DataAsset',
			'AdvancedFilter.system_filter' => 0,
		]);

		return $ret;
	}

	protected function _removeFilterField(Model $Model, $field)
	{
		$FilterValue = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');

		$ret = true;

		$ret &= (bool) $FilterValue->deleteAll([
			'AdvancedFilterValue.field' => $field,
			'AdvancedFilter.model' => $Model->alias
		]);

		return $ret;
	}

	protected function _updateFilterField(Model $Model, $field, $newField)
	{
		$FilterValue = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');

		$ret = true;

		// for the specific cases where audit filter field was negated, this should get it back
		if (isset($this->_invertFilterValue[$Model->alias]) && in_array($newField, $this->_invertFilterValue[$Model->alias])) {
			$ret &= (bool) $FilterValue->updateAll([
				'AdvancedFilterValue.value' => 'NOT AdvancedFilterValue.value'
			], [
				'AdvancedFilterValue.field' => $field,
				'AdvancedFilterValue.value' => ['0', '1'],
				'AdvancedFilter.model' => $Model->alias
			]);
		}

		$updateFields = [
			'AdvancedFilterValue.field' => "'{$newField}'"
		];

		$ret &= (bool) $FilterValue->updateAll($updateFields, [
			'AdvancedFilterValue.field' => $field,
			'AdvancedFilter.model' => $Model->alias
		]);

		return $ret;
	}

	protected function _updateStatusField(Model $Model, $field)
	{
		return $this->_updateFilterField($Model, $field, 'ObjectStatus_' . $field);
	}

	// additional All But Settings group ACL permissions configuration
	protected function _allButSettingsAcl()
	{
		$denyList = [
            'controllers/ldapConnectorAuthentications/edit'
        ];

        $Permission = ClassRegistry::init(array('class' => 'Permission', 'alias' => 'Permission'));
        $allButSettings = [
            'model' => 'Group',
            'foreign_key' => 13
        ];

        $Group = ClassRegistry::init('Group');
        $hasGroup = $Group->find('count', [
            'conditions' => [
                'Group.id' => 13
            ],
            'recursive' => -1
        ]);

        App::uses('CakeLog', 'Log');

        $ret = true;
        if ($hasGroup) {
        	ClassRegistry::init('Setting')->syncAcl();

            foreach ($denyList as $node) {
                $ret &= $r = $Permission->allow($allButSettings, $node, '*', -1);
                if (!$r) {
                    CakeLog::write('debug', 'Node ACL cannot be configured:' . $node);
                }
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $log = "Error occured when processing ACL Sync for All but settings group.";
                CakeLog::write('debug', "{$log}");
            }
        }

        return $ret;
	}

	// load a shell and return it
	public static function loadShell($name)
	{
		$Shell = new $name();
		$Shell->startup();

		return $Shell;
	}

}