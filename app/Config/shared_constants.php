<?php
/**
 * @deprecated This file is deprecated and all defined constants will be slowly moved and separated to relevant classes.
 */

define('NAME_SERVICE', 'Eramba');

// default name will be removed
define('DEFAULT_NAME', 'Eramba');

//used protocol
if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) {
	define('HTTP_PROTOCOL', 'https://');
}
else {
	define('HTTP_PROTOCOL', 'http://');
}

$apiUrl = Configure::read('Eramba.SUPPORT_API_URL');
define('STATS_REQUEST', $apiUrl . '/stats/');
define('HELP_REQUEST', $apiUrl . '/help/');

//colors to setions
define('COLOR_CONTROLS', '#94B86E');
define('COLOR_RISK', '#e25856');
define('COLOR_COMPLIANCE', '#f0ad4e');
define('COLOR_SECURITY', '#658db3');


define('DEFAULT_LOGO_WHITE', 'logo-white.png');
define('DEFAULT_LOGO_WHITE_URL', '/img/' . DEFAULT_LOGO_WHITE);

define('DEFAULT_LOGO', 'logo.png');
define('DEFAULT_LOGO_URL', '/img/' . DEFAULT_LOGO);

define('USER_NOTACTIVE', 0);
define('USER_ACTIVE', 1);

define('MEDIA_BASE_DIR', '\\media');
define('DATE_FORMAT', 'd.m.Y h:i');

/**
 * @deprecated for FlashComponent
 */
define('FLASH_OK', 'success'); // 'messages/flash-ok'
define('FLASH_ERROR', 'error'); // 'messages/flash-error'
define('FLASH_WARNING', 'warning'); // 'messages/flash-warning'
define('FLASH_INFO', 'default'); // 'messages/flash-info'

define('AWARENESS_PATH', WWW_ROOT . 'files/awareness/' );
define('AWARENESS_URL', '/files/awareness/' );

define('AWARENESS_FILE_EXAMPLES_PATH_HTML', AWARENESS_URL . 'examples' . DS);
define('AWARENESS_FILE_EXAMPLES_PATH', '.' . AWARENESS_FILE_EXAMPLES_PATH_HTML);

define('AWARENESS_VIDEO_PATH_HTML', AWARENESS_URL . 'videos' . DS);
define('AWARENESS_VIDEO_PATH', '.' . AWARENESS_VIDEO_PATH_HTML);

define('AWARENESS_QUESTIONNAIRE_PATH_HTML', AWARENESS_URL . 'questionnaires' . DS);
define('AWARENESS_QUESTIONNAIRE_PATH', '.' . AWARENESS_QUESTIONNAIRE_PATH_HTML);

define('AWARENESS_TEXT_FILE_PATH_HTML', AWARENESS_URL . 'text_files' . DS);
define('AWARENESS_TEXT_FILE_PATH', '.' . AWARENESS_TEXT_FILE_PATH_HTML);

//path to elements
define('CORE_ELEMENT_PATH', 'LimitlessTheme.core/');
define('FILTERS_ELEMENT_PATH', 'filters/');
define('ADVANCED_FILTERS_ELEMENT_PATH', 'advancedFilters/');
define('NOTIFICATION_SYSTEM_ELEMENT_PATH', 'notification_system/');

//pdf
define('PDF_DEFAULT_DEST', APP . 'Vendor' . DS . 'documents' . DS .'pdf'. DS);

//default page limit for all paginate sites
define('DEFAULT_PAGE_LIMIT', 20);

//asset_media_types
define('ASSET_MEDIA_TYPE_DATA', 1);
define('ASSET_MEDIA_TYPE_PEOPLE', 3);
define('ASSET_MEDIA_TYPE_HARDWARE', 4);
define('ASSET_MEDIA_TYPE_SOFTWARE', 5);
define('ASSET_MEDIA_TYPE_FACILITIES', 2);
define('BU_EVERYONE', 1);

//security_policy
define('SECURITY_POLICY_DRAFT', 0);
define('SECURITY_POLICY_RELEASED', 1);

//risk_mitigation_strategies
define('RISK_MITIGATION_ACCEPT', 1);
define('RISK_MITIGATION_AVOID', 2);
define('RISK_MITIGATION_MITIGATE', 3);
define('RISK_MITIGATION_TRANSFER', 4);


//security_service_types
define('SECURITY_SERVICE_DESIGN', 2);
define('SECURITY_SERVICE_PRODUCTION', 4);
define('SECURITY_SERVICE_RETIRED', 5);

define('SECURITY_SERVICE_ONGOING_CORRECTIVE_ACTIONS', 1);
define('SECURITY_SERVICE_NOT_ONGOING_CORRECTIVE_ACTIONS', 0);

//compliance_treatment_strategy
define('COMPLIANCE_TREATMENT_MITIGATE', 1); /* == */ define('COMPLIANCE_TREATMENT_COMPLIANT', 1);
define('COMPLIANCE_TREATMENT_NOT_APPLICABLE', 2);
define('COMPLIANCE_TREATMENT_NOT_COMPLIANT', 3);

//compliance_finding_statuses
define('COMPLIANCE_FINDING_OPEN', 1);
define('COMPLIANCE_FINDING_CLOSED', 2);

//compliance finding types
define('COMPLIANCE_FINDING_AUDIT', 1);
define('COMPLIANCE_FINDING_ASSESED', 2);

//audits
define('AUDIT_FAILED', 0);
define('AUDIT_PASSED', 1);

//project statuses
define('PROJECT_STATUS_PLANNED', 1);
define('PROJECT_STATUS_ONGOING', 2);
define('PROJECT_STATUS_COMPLETED', 3);

define('PROJECT_OVER_BUDGET', 1);
define('PROJECT_NOT_OVER_BUDGET', 0);

define('PROJECT_EXPIRED_TASKS', 1);
define('PROJECT_NOT_EXPIRED_TASKS', 0);

//exception statuses
define('EXCEPTION_CLOSED', 0);
define('EXCEPTION_OPEN', 1);

define('ADMIN_ID', 1);
define('ADMIN_GROUP_ID', 10);
define('AUDITEE_GROUP_ID', 11);

//workflow statuses
define('WORKFLOW_DRAFT', 0);
define('WORKFLOW_GET_VALIDATION', 1);
define('WORKFLOW_VALIDATED', 2);
define('WORKFLOW_GET_APPROVAL', 3);
define('WORKFLOW_APPROVED', 4);

define('WORKFLOW_ACKNOWLEDGMENT_EDIT', 'edit');
define('WORKFLOW_ACKNOWLEDGMENT_DELETE', 'delete');

define('NOTIFICATION_FEEDBACK_OK', 0);
define('NOTIFICATION_FEEDBACK_WAITING', 1);
define('NOTIFICATION_FEEDBACK_IGNORE', 2);

define('NOTIFICATION_OBJECT_ENABLED', 1);
define('NOTIFICATION_OBJECT_DISABLED', 0);

define('NOTIFICATION_TYPE_AWARENESS', 'awareness');
define('NOTIFICATION_TYPE_WARNING', 'warning');
define('NOTIFICATION_TYPE_DEFAULT', 'default');
define('NOTIFICATION_TYPE_REPORT', 'report');

define('COMPLIANCE_AUDIT_STATUS_NOT_EVIDENCE_NEEDED', 1);
define('COMPLIANCE_AUDIT_STATUS_EVIDENCE_PROVIDED', 2);
define('COMPLIANCE_AUDIT_STATUS_WAITING_FOR_EVIDENCE', 3);

define('SECURITY_INCIDENT_ONGOING', 2);
define('SECURITY_INCIDENT_CLOSED', 3);

define('SECURITY_INCIDENT_ONGOING_INCIDENT', 1);
define('SECURITY_INCIDENT_NOT_ONGOING_INCIDENT', 0);

define('SECURITY_POLICY_PROCEDURE', 'procedure');
define('SECURITY_POLICY_STANDARD', 'standard');
define('SECURITY_POLICY_POLICY', 'policy');

define('SECURITY_POLICY_PUBLIC', 'public');
define('SECURITY_POLICY_PRIVATE', 'private');
define('SECURITY_POLICY_LOGGED', 'logged');
define('SECURITY_POLICY_LDAP_GROUP', 'ldap_group');

define('SECURITY_POLICY_USE_CONTENT', 0);
define('SECURITY_POLICY_USE_ATTACHMENTS', 1);
define('SECURITY_POLICY_USE_URL', 2);

define('AWARENESS_PROGRAM_STARTED', 'started');
define('AWARENESS_PROGRAM_STOPPED', 'stopped');
define('AWARENESS_PROGRAM_STATS_UPDATE_SUCCESS', 1);
define('AWARENESS_PROGRAM_STATS_UPDATE_FAIL', 0);

define('AWARENESS_PROGRAM_MACRO_UID', 'UID');
define('AWARENESS_PROGRAM_MACRO_EMAIL', 'EMAIL');
define('AWARENESS_PROGRAM_MACRO_NAME', 'NAME');

define('PROGRAM_SCOPE_DRAFT', 'draft');
define('PROGRAM_SCOPE_DISCARDED', 'discarded');
define('PROGRAM_SCOPE_CURRENT', 'current');

define('PROGRAM_ISSUE_INTERNAL', 'internal');
define('PROGRAM_ISSUE_EXTERNAL', 'external');

define('PROGRAM_ISSUE_DRAFT', 'draft');
define('PROGRAM_ISSUE_DISCARDED', 'discarded');
define('PROGRAM_ISSUE_CURRENT', 'current');

define('TEAM_ROLE_ACTIVE', 'active');
define('TEAM_ROLE_DISCARDED', 'discarded');

define('GOAL_DRAFT', 'draft');
define('GOAL_DISCARDED', 'discarded');
define('GOAL_CURRENT', 'current');

define('POLICY_EXCEPTION_CLOSED', 0);
define('POLICY_EXCEPTION_OPEN', 1);

define('RISK_EXCEPTION_CLOSED', 0);
define('RISK_EXCEPTION_OPEN', 1);

define('COMPLIANCE_EXCEPTION_CLOSED', 0);
define('COMPLIANCE_EXCEPTION_OPEN', 1);

define('ITEM_STATUS_EXPIRED', 1);
define('ITEM_STATUS_NOT_EXPIRED', 0);

define('REVIEW_COMPLETE', 1);
define('REVIEW_NOT_COMPLETE', 0);

define('RISK_EXPIRED_REVIEWS', 1);
define('RISK_NOT_EXPIRED_REVIEWS', 0);

define('RISK_ABOVE_APPETITE', 1);
define('RISK_NOT_ABOVE_APPETITE', 0);

define('SERVICE_CONTRACT_EXPIRED', 1);
define('SERVICE_CONTRACT_NOT_EXPIRED', 0);

define('ISSUE_OPEN', 'open');
define('ISSUE_CLOSED', 'closed');

define('UPDATES_PATH', TMP . 'updates' . DS);

define('COMPARISON_EQUAL', 0);
define('COMPARISON_ABOVE', 1);
define('COMPARISON_UNDER', 2);

define('ADVANCED_FILTER_PARAM', '__filter');

define('ADVANCED_FILTER_DROPDOWN_DIVIDER', 'divider');

define('ADVANCED_FILTER_VALUE_MANY', 1);
define('ADVANCED_FILTER_VALUE_ONE', 0);

define('ADVANCED_FILTER_MULTISELECT_NONE', '___none___');
define('ADVANCED_FILTER_NONE_FIELD', '___none___');

define('ADVANCED_FILTER_DEFAULT_PAGE_LIMIT', 15);

define('ADVANCED_FILTER_LOG_INACTIVE', 0);
define('ADVANCED_FILTER_LOG_ACTIVE', 1);

define('ADVANCED_FILTER_NOT_PRIVATE', 0);
define('ADVANCED_FILTER_PRIVATE', 1);

define('COMPLIANCE_AUDIT_MACRO_AUDITCOMPLIANCELIST', 'AUDITCOMPLIANCELIST');
define('COMPLIANCE_AUDIT_MACRO_LOGINERAMBAURL', 'LOGINERAMBAURL');

define('RISKS_SECURITY_POLICIES_TREATMENT', 'treatment');
define('RISKS_SECURITY_POLICIES_INCIDENT', 'incident');

define('COMPLIANCE_AUDIT_STARTED', 'started');
define('COMPLIANCE_AUDIT_STOPPED', 'stopped');

define('LDAP_CONNECTOR_EMAIL_FETCH_EMAIL_ATTRIBUTE', 'email-attribute');
define('LDAP_CONNECTOR_EMAIL_FETCH_ACCOUNT_DOMAIN', 'account-domain');

define('LDAP_CONNECTOR_TYPE_GROUP', 'group');
define('LDAP_CONNECTOR_TYPE_AUTHENTICATOR', 'authenticator');

define('NOTIFICATION_PATH', APP . 'Vendor' . DS . 'notifications' . DS);
