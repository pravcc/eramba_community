<?php
class AdvancedFiltersNamesMigration
{
	public function migrationMap()
	{
		return [
			'Project' => [
				'owner_id' => 'Owner',
				'tag_title' => 'Tag-title',
				'tasks' => 'ProjectAchievement-description',
				'expenses' => 'ProjectExpense-description',
				'security_service_id' => 'SecurityService',
				'risk_id' => 'Risk',
				'third_party_risk_id' => 'ThirdPartyRisk',
				'business_continuity_id' => 'BusinessContinuity',
				'compliance_management_id' => 'CompliancePackageItem-item_id',
				'security_policy_id' => 'SecurityPolicy',
				'data_asset_id' => 'DataAsset',
			],
			'ProjectAchievement' => [
				'task_owner_id' => 'TaskOwner',
				'project_owner_id' => 'Project-Owner',
				'project_goal' => 'Project-goal',
				'project_start' => 'Project-start',
				'project_deadline' => 'Project-deadline',
				'project_status_id' => 'Project-project_status_id',
			],
			'SecurityService' => [
				'service_owner_id' => 'ServiceOwner',
				'collaborator_id' => 'Collaborator',
				'classifications' => 'Classification-name',
				'security_service_audit_date' => 'SecurityServiceAudit-planned_date',
				'audit_owner_id' => 'AuditOwner',
				'audit_evidence_owner_id' => 'AuditEvidenceOwner',
				'security_service_maintance_date' => 'SecurityServiceMaintenance-planned_date',
				'maintenance_owner_id' => 'MaintenanceOwner',
				'third_party_id' => 'CompliancePackage-third_party_id',
				'compliance_package_item_item_id' => 'CompliancePackageItem-item_id',
				'compliance_package_item_name' => 'CompliancePackageItem-name',
				'risk_id' => 'Risk',
				'third_party_risk_id' => 'ThirdPartyRisk',
				'business_continuity_id' => 'BusinessContinuity',
				'data_asset_id' => 'DataAsset',
				'project_id' => 'Project',
				'security_policy_id' => 'SecurityPolicy',
				'service_contract_id' => 'ServiceContract',
			],
			'SecurityServiceAudit' => [
				'audit_owner_id' => 'AuditOwner',
				'audit_evidence_owner_id' => 'AuditEvidenceOwner',
				'third_party_id' => 'CompliancePackage-third_party_id',
				'risk_id' => 'SecurityService-Risk',
				'third_party_risk_id' => 'SecurityService-ThirdPartyRisk',
				'business_continuity_id' => 'SecurityService-BusinessContinuity',
				'data_asset_id' => 'SecurityService-DataAsset',
				'security_incident_id' => 'SecurityService-SecurityIncident'
			],
			'SecurityServiceMaintenance' => [
				'maintenance_owner_id' => 'MaintenanceOwner',
				'third_party_id' => 'CompliancePackage-third_party_id',
				'risk_id' => 'SecurityService-Risk',
				'third_party_risk_id' => 'SecurityService-ThirdPartyRisk',
				'business_continuity_id' => 'SecurityService-BusinessContinuity',
				'data_asset_id' => 'SecurityService-DataAsset',
			],
			'SecurityPolicy' => [
				'owner_id' => 'Owner',
				'collaborator_id' => 'Collaborator',
				'tag_title' => 'Tag-title',
				'risk_id' => 'RiskIncident',
				'risk_treatment_id' => 'RiskTreatment',
				'third_party_risk_id' => 'ThirdPartyRiskIncident',
				'third_party_risk_treatment_id' => 'ThirdPartyRiskTreatment',
				'business_continuity_id' => 'BusinessContinuityIncident',
				'business_continuity_treatment_id' => 'BusinessContinuityTreatment',
				'security_service_id' => 'SecurityService',
				'third_party_id' => 'CompliancePackage-third_party_id',
				'compliance_package_item_item_id' => 'CompliancePackageItem-item_id',
				'compliance_package_item_name' => 'CompliancePackageItem-name',
			],
			'SecurityPolicyReview' => [
				'parent' => 'foreign_key'
			],
			'AssetReview' => [
				'parent' => 'foreign_key'
			],
			'RiskReview' => [
				'parent' => 'foreign_key'
			],
			'ThirdPartyRiskReview' => [
				'parent' => 'foreign_key'
			],
			'BusinessContinuityReview' => [
				'parent' => 'foreign_key'
			],
			'Risk' => [
				'tag_title' => 'Tag-name',
				'risk_classification_id' => 'RiskClassification',
				'analysis_risk_appetite_threshold_id' => 'RiskAppetiteThresholdAnalysis',
				'asset_id' => 'Asset',
				'asset_business_unit_id' => 'Asset-BusinessUnit',
				'data_asset_id' => 'DataAsset',
				'vulnerability_id' => 'Vulnerability',
				'threat_id' => 'Threat',
				'treatment_risk_classification_id' => 'RiskClassificationTreatment',
				'treatment_risk_appetite_threshold_id' => 'RiskAppetiteThresholdTreatment',
				'risk_exception_id' => 'RiskException-id',
				'security_service_id' => 'SecurityService-id',
				'project_id' => 'Project-id',
				'security_policy_id' => 'SecurityPolicyTreatment-id',
				'ObjectStatus_expired' => 'ObjectStatus_expired_reviews',
			],
			'ThirdPartyRisk' => [
				'tag_title' => 'Tag-name',
				'risk_classification_id' => 'RiskClassification',
				'analysis_risk_appetite_threshold_id' => 'RiskAppetiteThresholdAnalysis',
				'asset_id' => 'Asset',
				'third_party_id' => 'ThirdParty',
				'data_asset_id' => 'DataAsset',
				'vulnerability_id' => 'Vulnerability',
				'threat_id' => 'Threat',
				'treatment_risk_classification_id' => 'RiskClassificationTreatment',
				'treatment_risk_appetite_threshold_id' => 'RiskAppetiteThresholdTreatment',
				'risk_exception_id' => 'RiskException-id',
				'security_service_id' => 'SecurityService-id',
				'project_id' => 'Project-id',
				'security_policy_id' => 'SecurityPolicyTreatment-id',
				'ObjectStatus_expired' => 'ObjectStatus_expired_reviews',
			],
			'BusinessContinuity' => [
				'tag_title' => 'Tag-name',
				'risk_classification_id' => 'RiskClassification',
				'analysis_risk_appetite_threshold_id' => 'RiskAppetiteThresholdAnalysis',
				'business_unit_id' => 'BusinessUnit',
				'process_id' => 'Process',
				'process_rpd' => 'Process-rpd',
				'process_rto' => 'Process-rto',
				'process_rpo' => 'Process-rpo',
				'data_asset_id' => 'DataAsset',
				'vulnerability_id' => 'Vulnerability',
				'threat_id' => 'Threat',
				'treatment_risk_classification_id' => 'RiskClassificationTreatment',
				'treatment_risk_appetite_threshold_id' => 'RiskAppetiteThresholdTreatment',
				'risk_exception_id' => 'RiskException-id',
				'security_service_id' => 'SecurityService-id',
				'project_id' => 'Project-id',
				'security_policy_id' => 'SecurityPolicyTreatment-id',
				'business_continuity_plan_id' => 'BusinessContinuityPlan',
				'ObjectStatus_expired' => 'ObjectStatus_expired_reviews',
			],
			'ProgramIssue' => [
				'type' => 'ProgramIssueType.type'
			],
			'Goal' => [
				'security_service_id' => 'SecurityService',
				'risk_id' => 'Risk',
				'third_party_risk_id' => 'ThirdPartyRisk',
				'business_continuity_id' => 'BusinessContinuity',
				'project_id' => 'Project',
				'security_policy_id' => 'SecurityPolicy',
				'program_issue_id' => 'ProgramIssue'
			],
			'BusinessUnit' => [
				'legal_id' => 'Legal',
				'business_unit_owner_id' => 'BusinessUnitOwner'
			],
			'Legal' => [
				'legal_advisor_id' => 'LegalAdvisor',
			],
			'ThirdParty' => [
				'sponsor_id' => 'Sponsor',
				'legal_id' => 'Legal'
			],
			'SystemLog' => [
				'user' => 'User-login',
			],
			'VendorAssessmentSystemLog' => [
				'user' => 'User-login',
			],
			'AccountReviewPullSystemLog' => [
				'user' => 'User-login',
				'foreign_key' => 'AccountReviewPull-hash',
			],
			'User' => [
				'portal_id' => 'Portal',
				'group_id' => 'Group'
			],
			'Asset' => [
				'business_unit_id' => 'BusinessUnit',
				'legal_id' => 'Legal',
				'related_asset_id' => 'RelatedAsset',
				'asset_owner_id' => 'AssetOwner',
				'asset_guardian_id' => 'AssetGuardian',
				'asset_user_id' => 'AssetUser',
				'asset_classification_id' => 'AssetClassification',
				'security_incident_status' => 'SecurityIncident-security_incident_status_id',
				'third_party' => 'CompliancePackage-third_party_id',
				'compliance_package_number' => 'CompliancePackage-package_id',
				'compliance_package_name' => 'CompliancePackage-name',
				'compliance_package_item_number' => 'CompliancePackageItem-item_id',
				'compliance_package_item_name' => 'CompliancePackageItem-name',
				'data_asset_status' => 'DataAsset-data_asset_status_id'
				// removed fields
				// account_review_title, data_asset_title, security_incident_title, risk_title
			],
			'DataAssetInstance' => [
				'asset_owner_id' => 'Asset-AssetOwner',
				'gdpr_enabled' => 'DataAssetSetting-gdpr_enabled',
				'driver_for_compliance' => 'DataAssetSetting-driver_for_compliance',
				'dpo' => 'DataAssetSetting-Dpo',
				'processor' => 'DataAssetSetting-Processor',
				'controller' => 'DataAssetSetting-Controller',
				'controller_representative' => 'DataAssetSetting-ControllerRepresentative',
				'supervisory_authority' => 'SupervisoryAuthority-country_id',
			],
			'DataAsset' => [
				'asset_id' => 'DataAssetInstance-asset_id',
				'business_unit_id' => 'BusinessUnit',
				'third_party_id' => 'ThirdParty',
				'risk_id' => 'Risk',
				'third_party_risk_id' => 'ThirdPartyRisk',
				'business_continuity_id' => 'BusinessContinuity',
				'security_service_id' => 'SecurityService',
				'security_policy_id' => 'SecurityPolicy',
				'project_id' => 'Project',
				'data_asset_gdpr_data_type' => 'DataAssetGdprDataType-data_type',
				'purpose' => 'DataAssetGdpr-purpose',
				'right_to_be_informed' => 'DataAssetGdpr-right_to_be_informed',
				'data_subject' => 'DataAssetGdpr-data_subject',
				'data_asset_gdpr_collection_method' => 'DataAssetGdprCollectionMethod-collection_method',
				'volume' => 'DataAssetGdpr-volume',
				'recived_data' => 'DataAssetGdpr-recived_data',
				'data_asset_gdpr_lawful_base' => 'DataAssetGdprLawfulBase-lawful_base',
				'contracts' => 'DataAssetGdpr-contracts',
				'stakeholders' => 'DataAssetGdpr-stakeholders',
				'accuracy' => 'DataAssetGdpr-accuracy',
				'right_to_access' => 'DataAssetGdpr-right_to_access',
				'right_to_rectification' => 'DataAssetGdpr-right_to_rectification',
				'right_to_decision' => 'DataAssetGdpr-right_to_decision',
				'right_to_object' => 'DataAssetGdpr-right_to_object',
				'retention' => 'DataAssetGdpr-retention',
				'encryption' => 'DataAssetGdpr-encryption',
				'right_to_erasure' => 'DataAssetGdpr-right_to_erasure',
				'data_asset_gdpr_archiving_driver' => 'DataAssetGdprArchivingDriver-archiving_driver',
				'origin' => 'DataAssetGdpr-origin',
				'destination' => 'DataAssetGdpr-destination',
				'transfer_outside_eea' => 'DataAssetGdpr-transfer_outside_eea',
				'third_party_involved' => 'ThirdPartyInvolved-country_id',
				'third_party_involved_all' => 'DataAssetGdpr-third_party_involved_all',
				'data_asset_gdpr_third_party_country' => 'DataAssetGdprThirdPartyCountry-third_party_country',
				'security' => 'DataAssetGdpr-security',
				'right_to_portability' => 'DataAssetGdpr-right_to_portability',
			],
			'PolicyException' => [
				'requestor_id' => 'Requestor',
				'classification_name' => 'Classification-name',
				'asset_id' => 'Asset',
				'security_policy_id' => 'SecurityPolicy',
			],
			'RiskException' => [
				'requester_id' => 'Requester',
				'tag_title' => 'Tag-title',
				'risk_id' => 'Risk',
				'third_party_risk_id' => 'ThirdPartyRisk',
				'business_continuity_id' => 'BusinessContinuity',
			],
			'ComplianceException' => [
				'requestor_id' => 'Requestor',
				'tag_title' => 'Tag-title',
				'third_party_id' => 'CompliancePackage-third_party_id',
				'compliance_package_item_item_id' => 'CompliancePackageItem-item_id',
				'compliance_package_item_name' => 'CompliancePackageItem-name',
			],
			'ServiceContract' => [
				'service_contract_owner_id' => 'Owner',
			],
			'ComplianceAnalysisFinding' => [
				'tag_title' => 'Tag-title',
				'owner_id' => 'Owner',
				'collaborator_id' => 'Collaborator',
				'third_party_id' => 'CompliancePackage-third_party_id',
				'compliance_package_item_item_id' => 'CompliancePackageItem-item_id',
				'compliance_package_item_name' => 'CompliancePackageItem-name',
			],
			'ComplianceManagement' => [
				'third_party' => 'CompliancePackage-third_party_id',
				'item_id' => 'CompliancePackageItem-item_id',
				'item_name' => 'CompliancePackageItem-name',
				'item_description' => 'CompliancePackageItem-description',
				'package_id' => 'CompliancePackage-package_id',
				'package_name' => 'CompliancePackage-name',
				'package_description' => 'CompliancePackage-description',
				'project_id' => 'Project',
				'asset_id' => 'Asset',
				'security_service_id' => 'SecurityService',
				'security_policy_id' => 'SecurityPolicy',
				'compliance_exception_id' => 'ComplianceException',
				'risk_id' => 'Risk',
				'third_party_risk_id' => 'ThirdPartyRisk',
				'business_continuity_id' => 'BusinessContinuity',
				'legal_id' => 'Legal',
				'compliance_analysis_finding_id' => 'ComplianceAnalysisFinding',
			],
			'CompliancePackageItem' => [
				'third_party_id' => 'CompliancePackage-third_party_id',
				'package_id' => 'CompliancePackage-package_id',
				'package_name' => 'CompliancePackage-name',
				'package_description' => 'CompliancePackage-description',
			],
			'SecurityIncident' => [
				'classification_id' => 'Classification-name',
				'asset_risk_id' => 'AssetRisk',
				'asset_risk_policy_incident_id' => 'AssetRisk-SecurityPolicyIncident',
				'third_party_risk_id' => 'ThirdPartyRisk',
				'third_party_risk_policy_incident_id' => 'ThirdPartyRisk-SecurityPolicyIncident',
				'business_risk_id' => 'BusinessContinuity',
				'business_continuity_policy_incident_id' => 'BusinessContinuity-SecurityPolicyIncident',
				'owner_id' => 'Owner',
				'security_service_id' => 'SecurityService',
				'asset_id' => 'Asset',
				'third_party_id' => 'ThirdParty',
			],
			'SecurityIncidentStagesSecurityIncident' => [
				'stage_name' => 'SecurityIncidentStage-name',
				'stage_description' => 'SecurityIncidentStage-description',
			],
			'VendorAssessment' => [
				'auditor_id' => 'Auditor',
				'auditee_id' => 'Auditee',
				'third_party_id' => 'ThirdParty',
				'tag_title' => 'Tag-title',
			],
			'VendorAssessments.VendorAssessment' => [
				'auditor_id' => 'Auditor',
				'auditee_id' => 'Auditee',
				'third_party_id' => 'ThirdParty',
				'tag_title' => 'Tag-title',
			],
			'VendorAssessmentFeedback' => [
				'question_chapter_number' => 'VendorAssessmentQuestion-chapter_number',
				'question_chapter_title' => 'VendorAssessmentQuestion-chapter_title',
				'question_chapter_description' => 'VendorAssessmentQuestion-chapter_description',
				'question_number' => 'VendorAssessmentQuestion-number',
				'question_title' => 'VendorAssessmentQuestion-title',
				'question_description' => 'VendorAssessmentQuestion-description',
				'answer_type' => 'VendorAssessmentQuestion-answer_type',
				'option' => 'VendorAssessmentOption-title',
				'widget_type' => 'VendorAssessmentQuestion-widget_type',
			],
			'VendorAssessments.VendorAssessmentFeedback' => [
				'question_chapter_number' => 'VendorAssessmentQuestion-chapter_number',
				'question_chapter_title' => 'VendorAssessmentQuestion-chapter_title',
				'question_chapter_description' => 'VendorAssessmentQuestion-chapter_description',
				'question_number' => 'VendorAssessmentQuestion-number',
				'question_title' => 'VendorAssessmentQuestion-title',
				'question_description' => 'VendorAssessmentQuestion-description',
				'answer_type' => 'VendorAssessmentQuestion-answer_type',
				'option' => 'VendorAssessmentOption-title',
				'widget_type' => 'VendorAssessmentQuestion-widget_type',
			],
			'VendorAssessmentFinding' => [
				'auditor_id' => 'Auditor',
				'auditee_id' => 'Auditee',
				'tag_title' => 'Tag-title',
				'question_chapter_number' => 'VendorAssessmentQuestion-chapter_number',
				'question_chapter_title' => 'VendorAssessmentQuestion-chapter_title',
				'question_chapter_description' => 'VendorAssessmentQuestion-chapter_description',
				'question_number' => 'VendorAssessmentQuestion-number',
				'question_title' => 'VendorAssessmentQuestion-title',
				'question_description' => 'VendorAssessmentQuestion-description',
				'expired' => 'ObjectStatus_expired'
			],
			'VendorAssessments.VendorAssessmentFinding' => [
				'auditor_id' => 'Auditor',
				'auditee_id' => 'Auditee',
				'tag_title' => 'Tag-title',
				'question_chapter_number' => 'VendorAssessmentQuestion-chapter_number',
				'question_chapter_title' => 'VendorAssessmentQuestion-chapter_title',
				'question_chapter_description' => 'VendorAssessmentQuestion-chapter_description',
				'question_number' => 'VendorAssessmentQuestion-number',
				'question_title' => 'VendorAssessmentQuestion-title',
				'question_description' => 'VendorAssessmentQuestion-description',
				'expired' => 'ObjectStatus_expired'
			],
			'AwarenessProgram' => [
				'security_policy_id' => 'SecurityPolicy',
			],
			'AwarenessProgramActiveUser' => [
				'awareness_program_status' => 'AwarenessProgram-status',
			],
			'AwarenessProgramCompliantUser' => [
				'awareness_program_status' => 'AwarenessProgram-status',
			],
			'AwarenessProgramIgnoredUser' => [
				'awareness_program_status' => 'AwarenessProgram-status',
			],
			'AwarenessProgramNotCompliantUser' => [
				'awareness_program_status' => 'AwarenessProgram-status',
			],
			'AwarenessReminder' => [
				'awareness_program_status' => 'AwarenessProgram-status',
			],
			'AccountReview' => [
				'asset_id' => 'Asset',
				'owner' => 'Owner',
				'reviewer' => 'Reviewer',
				'tag_title' => 'Tag-title'
			],
			'AccountReviews.AccountReview' => [
				'asset_id' => 'Asset',
				'owner' => 'Owner',
				'reviewer' => 'Reviewer',
				'tag_title' => 'Tag-title'
			],
			'AccountReviewPull' => [
				'asset_id' => 'Asset',
				'account_review_type' => 'AccountReview-type',
			],
			'AccountReviews.AccountReviewPull' => [
				'asset_id' => 'Asset',
				'account_review_type' => 'AccountReview-type',
			],
			'AccountReviewFeedback' => [
				'account_review_id' => 'AccountReviewPull-account_review_id',
				'account_review_type' => 'AccountReview-type',
				'pull_hash' => 'AccountReviewPull-hash',
				'pull_created' => 'AccountReviewPull-created',
				'user' => 'AccountReviewFeedRow-user',
				'role_name' => 'AccountReviewFeedbackRole-name',
				'role_type' => 'AccountReviewFeedbackRole-type',
				'description' => 'AccountReviewFeedRow-description',
				'tag_title' => 'Tag-title',
			],
			'AccountReviews.AccountReviewFeedback' => [
				'account_review_id' => 'AccountReviewPull-account_review_id',
				'account_review_type' => 'AccountReview-type',
				'pull_hash' => 'AccountReviewPull-hash',
				'pull_created' => 'AccountReviewPull-created',
				'user' => 'AccountReviewFeedRow-user',
				'role_name' => 'AccountReviewFeedbackRole-name',
				'role_type' => 'AccountReviewFeedbackRole-type',
				'description' => 'AccountReviewFeedRow-description',
				'tag_title' => 'Tag-title',
			],
			'AccountReviewFinding' => [
				'account_review_id' => 'AccountReviewPull-account_review_id',
				'account_review_type' => 'AccountReview-type',
				'pull_hash' => 'AccountReviewPull-hash',
				'pull_created' => 'AccountReviewPull-created',
				'feedback_user' => 'AccountReviewFeedRow.user',
				'feedback_type' => 'AccountReviewFeedback-type',
				'owner_id' => 'Owner',
				'reviewer_id' => 'Reviewer',
				'tag_title' => 'Tag-title',
			],
			'AccountReviews.AccountReviewFinding' => [
				'account_review_id' => 'AccountReviewPull-account_review_id',
				'account_review_type' => 'AccountReview-type',
				'pull_hash' => 'AccountReviewPull-hash',
				'pull_created' => 'AccountReviewPull-created',
				'feedback_user' => 'AccountReviewFeedRow.user',
				'feedback_type' => 'AccountReviewFeedback-type',
				'owner_id' => 'Owner',
				'reviewer_id' => 'Reviewer',
				'tag_title' => 'Tag-title',
			],
			'BusinessContinuityPlan' => [
				'owner_id' => 'Owner',
				'sponsor_id' => 'Sponsor',
				'launch_initiator_id' => 'LaunchInitiator',
			],
			'BusinessContinuityPlanAdmin' => [
				'owner_id' => 'Owner',
				'sponsor_id' => 'Sponsor',
				'launch_initiator_id' => 'LaunchInitiator',
			],
			'BusinessContinuityTask' => [
				'awareness_role' => 'AwarenessRole',
			]
		];
	}

	public function migrate($migrationMap = null)
	{
		if ($migrationMap === null) {
			$migrationMap->migrationMap();
		}

		$ret = true;

		$AdvancedFilters = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		$AdvancedFilterValues = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');

		foreach ($migrationMap as $modelName => $fields) {
			$filterIds = $AdvancedFilters->find('list', [
				'conditions' => [
					'AdvancedFilter.model' => $modelName
				],
				'fields' => [
					'AdvancedFilter.id'
				],
				'recursive' => -1
			]);

			if (empty($filterIds)) {
				continue;
			}

			foreach ($fields as $old => $new) {
				$ret &= (bool) $AdvancedFilterValues->updateAll(['field' => '"' . $new . '"'], [
					'AdvancedFilterValue.advanced_filter_id' => $filterIds,
					'AdvancedFilterValue.field' => $old
				]);
				$ret &= (bool) $AdvancedFilterValues->updateAll(['field' => '"' . $new . '__show"'], [
					'AdvancedFilterValue.advanced_filter_id' => $filterIds,
					'AdvancedFilterValue.field' => $old . '__show'
				]);
				$ret &= (bool) $AdvancedFilterValues->updateAll(['field' => '"' . $new . '__comp_type"'], [
					'AdvancedFilterValue.advanced_filter_id' => $filterIds,
					'AdvancedFilterValue.field' => $old . '__comp_type'
				]);
				$ret &= (bool) $AdvancedFilterValues->updateAll(['field' => '"' . $new . '__use_calendar"'], [
					'AdvancedFilterValue.advanced_filter_id' => $filterIds,
					'AdvancedFilterValue.field' => $old . '__use_calendar'
				]);
			}
		}

		return $ret;
	}
}