<?php
App::uses('AppModule', 'Lib');
App::uses('CustomField', 'CustomFields.Model');

if (!function_exists('ldap_escape')) {
	if (!defined('LDAP_ESCAPE_FILTER')) {
		define('LDAP_ESCAPE_FILTER', 1);
	}

	if (!defined('LDAP_ESCAPE_DN')) {
		define('LDAP_ESCAPE_DN', 2);
	}

	function ldap_escape($subject, $ignore = NULL, $filter = LDAP_ESCAPE_FILTER) {
		$dn = ($filter == LDAP_ESCAPE_FILTER) ? false : true;
		
		// The base array of characters to escape
		// Flip to keys for easy use of unset()
		$search = array_flip($dn ? array('\\', ',', '=', '+', '<', '>', ';', '"', '#') : array('\\', '*', '(', ')', "\x00"));

		// Process characters to ignore
		if (is_array($ignore)) {
			$ignore = array_values($ignore);
		}
		for ($char = 0; isset($ignore[$char]); $char++) {
			unset($search[$ignore[$char]]);
		}

		// Flip $search back to values and build $replace array
		$search = array_keys($search); 
		$replace = array();
		foreach ($search as $char) {
			$replace[] = sprintf('\\%02x', ord($char));
		}

		// Do the main replacement
		$result = str_replace($search, $replace, $subject);

		// Encode leading/trailing spaces in DN values
		if ($dn) {
			if ($result[0] == ' ') {
				$result = '\\20'.substr($result, 1);
			}
			if ($result[strlen($result) - 1] == ' ') {
				$result = substr($result, 0, -1).'\\20';
			}
		}

		return $result;
	}
}

/**
 * Returns a model instance for later use.
 * 
 * @param  mixed $model  String of a model name or Instance of Model class.
 * @return Model
 * 
 * @deprecated
 */
function _getModelInstance($model) {
	if ($model instanceof AppModel) {
		return $model;
	}
	else {
		$models = App::objects('Model');
		foreach (AppModule::getAllModules() as $module) {
			$models = am($models, App::objects($module . '.Model'));
		}

		if (!in_array($model, $models)) {
			trigger_error(__("Requested model instance '%s' does not exist", $model));
		}

		App::uses($model, 'Model');
		return ClassRegistry::init($model);
	}
}

/**
 * Initialize a Helper class instance.
 */
function _getHelperInstance($helper) {
	$baseHelper = $helper;
	$baseHelperClass = $baseHelper . 'Helper';

	App::import('Helper', $baseHelper);
	App::uses('View', 'View');

	return new $baseHelperClass(new View());
}

/**
 * Generic function for gettting model's label name.
 */
function getModelLabel($model, $options = array()) {
	$_m = _getModelInstance($model);

	return $_m->label($options);
}

/**
 * Generic function to get object's title.
 *
 * @todo Cache.
 */
function getItemTitle($model, $id) {
	$_m = _getModelInstance($model);

	return $_m->getRecordTitle($id);
}

/**
 * Upgraded SSL check for various server platforms.
 */
function isSSL(){
	// apache
	if (env('HTTPS')) {
		return true;
	}
	// nginx
	elseif (env('HTTPS') == 'on'){
		return true;
	}
	elseif (env('SERVER_PORT') == 443) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Return list of custom currencies available in the app.
 */
function getCustomCurrencies() {
	require APP . 'Config' . DS . 'currencies.php';

	return $customCurrencies;
}

/**
 * Handle developer errors throughout the application.
 * @param  string  $description Error description.
 * @param  integer $code           Type of error (E_NOTICE, E_WARNING, E_ERROR).
 */
function AppError($description, $code = E_NOTICE) {
	list($error, $log) = ErrorHandler::mapErrorCode($code);

	$backtrace = debug_backtrace();
	$file = $backtrace[0]['file'];
	$line = $backtrace[0]['line'];
	unset($backtrace);

	$data = array(
		'level' => $log,
		'code' => 'Eramba',
		'error' => $error,
		'description' => $description/* . "\n"*/,
		'file' => $file,
		'line' => $line,
		'start' => 2,
		'context' => array(),
		'path' => Debugger::trimPath($file)
	);
	
	$debug = Configure::read('debug');
	if (!empty($debug)) {
		// not showing html notices about what happened anymore.
		// Debugger::getInstance()->outputError($data);
	}

	$message = sprintf('%s in [%s, line %s]', $error, $file, $line);
	$message .= "\n" . $description . "\n";

	return CakeLog::write('eramba', $message);
}

function ddd($data, $showHtml = null) {
	if (Configure::read('debug')) {
		$backtrace = debug_backtrace(false, 1);

		if (function_exists('dd')) {
			pr('ddd-location: ' . $backtrace[0]['file'] . ':' . $backtrace[0]['line']);
			call_user_func_array('dd', func_get_args());
		}
		else {
			if (is_object($data)) {
				$data = Debugger::exportVar($data, 4);
			}
			
			debug($data, $showHtml, false);
			pr('dd-location: ' . $backtrace[0]['file'] . ':' . $backtrace[0]['line']);
			die();
		}
	}
}

function getQueryLogs() {
	App::uses('ConnectionManager', 'Model');
	$db = ConnectionManager::getDataSource('default');

	if (!method_exists($db, 'getLog')) {
		return array();
	}

	$log = $db->getLog(false, false);

	return array(
		'time' => $log['time'],
		'count' => $log['count'],
		'count_formatted' => CakeNumber::format($log['count'], array(
			'before' => false,
			'places' => 0,
			'thousands' => ' ',
			'decimals' => false
		))
	);
}

function scriptExecutionTime() {
	if (empty($_SERVER["REQUEST_TIME_FLOAT"])) {
		return false;
	}
	
	return microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
}

/**
 * Return all available languages for this application.
 */
function availableLangs() {
	return array(
		'eng' => __('Default (English)'),
		'deu' => __('German'),
		// 'en_us' => __('English (US)'),
		'spa' => __('Spanish (Argentina)'),
		'hrv' => __('Croatian'),
		'rus' => __('Russian'),
		'por' => __('Portuguese'),
		'fra' => __('French'),
		'ita' => __('Italian'),
		// 'nld' => __('Dutch'),
		// 'kor' => __('Korean')
	);
}

/**
 * Checks if a certain language is available in the system.
 */
function langExists($lang) {
	$available = availableLangs();
	return isset($available[$lang]);
}

/**
 * All available languages in format (except the default which is defined in constants.php):
 * ISO-639-1 => ISO-639-2
 *
 * @see ISO link: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
function availableLocals() {
	return array(

	);
}

/**
 * Return local in format ISO-639-2.
 *
 * @param string $lang locale ISO-639-1
 */
function getCakeLocale($lang) {
	$langs = availableLocals();

	if (isset($langs[$lang])) {
		return $langs[$lang];
	}
	else {
		return null;
	}
}

/**
 * Returns locale in format ISO-639-1.
 *
 * @param string $lang locale ISO-639-2
 */
function getHtmlLocale($lang) {
	$langs = array_flip(availableLocals());

	if (isset($langs[$lang])) {
		return $langs[$lang];
	}
	else {
		return null;
	}
}

/**
 * @deprecated Use your own function for appropriate situation instead
 */
function getEmptyValue($value, $forceNum = false) {
	if (!is_array($value)) {
		$value = trim($value);
	}

	if ($forceNum && is_numeric($value)) {
		return $value;
	}
	
	if (!empty($value)) {
		return nl2br($value);
	}

	return '-';
}

function getCustomFieldItemValue($item, $customField) {
	$default = false;
	if (!empty($item['CustomFieldValue'])) {
		foreach ($item['CustomFieldValue'] as $v) {
			if ($v['custom_field_id'] == $customField['id']) {
				$default = $v['value'];
				break;
			}
		}
	}

	if ($customField['type'] == CustomField::TYPE_DROPDOWN) {
		foreach ($customField['CustomFieldOption'] as $option) {
			if ($option['id'] == $default) {
				$default = $option['value'];
				break;
			}
		}
	}

	return getEmptyValue($default);
}

/**
 * Is currently logged user admin.
 */
function isAdmin($logged) {
	return isset($logged['Groups']) && is_array($logged['Groups']) && in_array(ADMIN_GROUP_ID, $logged['Groups']);
}

function getResidualRisk($residual_score, $risk_score) {
	return CakeNumber::precision(($residual_score / 100) * $risk_score, 2);
}

function getResidualRiskFormula($residual_score, $risk_score) {
	return sprintf('(%s / 100) x %s = %s',
		$residual_score,
		$risk_score,
		getResidualRisk($residual_score, $risk_score)
	);
	// return '(' . $residual_score . '/' . 100 . ') x ' . $risk_score . '('.__('Risk Score').')';
}

function getWorkflowStatuses() {
	return array(
		WORKFLOW_DRAFT => __('Draft'),
		WORKFLOW_GET_VALIDATION => __('Validation Request Sent'),
		WORKFLOW_VALIDATED => __('Validation Executed'),
		WORKFLOW_GET_APPROVAL => __('Approval Request Sent'),
		WORKFLOW_APPROVED => __('Approval Executed')
	);
}

function getAuditStatuses($status = null, $arr = null) {
	$types = array(
		0 => __('Fail'),
		1 => __('Pass')
	);

	if ($status === null || $arr) {
		return $types;
	}
	else {
		return $types[$status];
	}
}

function getPolicyExceptionStatuses() {
	return getCommonStatuses();
}

function getCommonStatuses() {
	return array(
		0 => __('Closed'),
		1 => __('Open')
	);
}

function getFindingTypes() {
	return array(
		COMPLIANCE_FINDING_AUDIT => __('Audit Finding'),
		COMPLIANCE_FINDING_ASSESED => __('Assessment'),
	);
}

function getComplianceAuditSettingStatuses($status, $type, $arr = null) {

	$statuses['labelText'] = array(
		COMPLIANCE_AUDIT_STATUS_NOT_EVIDENCE_NEEDED => __('No Evidence Needed'),
		COMPLIANCE_AUDIT_STATUS_EVIDENCE_PROVIDED => __('Evidence Provided'),
		COMPLIANCE_AUDIT_STATUS_WAITING_FOR_EVIDENCE => __('Waiting for Evidence')
	);

	$statuses['labelClass'] = array(
		COMPLIANCE_AUDIT_STATUS_NOT_EVIDENCE_NEEDED => 'label-success',
		COMPLIANCE_AUDIT_STATUS_EVIDENCE_PROVIDED => 'label-success',
		COMPLIANCE_AUDIT_STATUS_WAITING_FOR_EVIDENCE => 'label-success'
	);

	if($arr){
		return $statuses['labelText'];
	}
	else{
		return $statuses[$type][$status];
	}
}

function getSystemRecordTypes($status, $arr = null) {
	$types = array(
		1 => __('Insert'),
		2 => __('Update'),
		3 => __('Delete'),
		4 => __('Login'),
		5 => __('Wrong Login')
	);

	if ($arr) {
		return $types;
	}
	else {
		return $types[$status];
	}
}

function getSecurityPolicyStatuses() {
	return array(
		0 => __( 'Draft' ),
		1 => __( 'Published' )
	);
}

function getStatusFilterOption(){
	return array(
		1 => __('Yes'),
		0 => __('No')
	);
}

function getMitigationStrategies() {
	return array(
		RISK_MITIGATION_ACCEPT => __('Accept'),
		RISK_MITIGATION_AVOID => __('Avoid'),
		RISK_MITIGATION_MITIGATE => __('Mitigate'),
		RISK_MITIGATION_TRANSFER => __('Transfer')
	);
}

function getLdapConnectorEmailFetchTypes() {
	return array(
		LDAP_CONNECTOR_EMAIL_FETCH_EMAIL_ATTRIBUTE => __('LDAP Email Attribute'),
		LDAP_CONNECTOR_EMAIL_FETCH_ACCOUNT_DOMAIN => __('Define Domain on the field below')
	);
}

function getSecurityIncidentTypes($type = null) {
	$types = array(
		'event' => __('Event'),
		'possible-incident' => __('Possible Incident'),
		'incident' => __('Incident')
	);

	if (empty($type)) {
		return $types;
	}

	return $types[$type];
}

function getSecurityIncidentStageStatus() {
	return array(
		1 => __('Completed'),
		0 => __('Initiated')
	);
}

function getPoliciesDocumentTypes($type = null) {
	$types = array(
		SECURITY_POLICY_POLICY => __('Policy'),
		SECURITY_POLICY_STANDARD => __('Standard'),
		SECURITY_POLICY_PROCEDURE => __('Procedure')
	);

	if (empty($type)) {
		return $types;
	}

	return $types[$type];
}

function getPoliciesDocumentPermissions() {
	return array(
		SECURITY_POLICY_PUBLIC => __('Public (Everyone can see the document)'),
		SECURITY_POLICY_PRIVATE => __('Private (Document is not shown on the portal)'),
		SECURITY_POLICY_LOGGED => __('Authorized Users Only'),
		// SECURITY_POLICY_LDAP_GROUP => __('LDAP Group')
	);
}

function getPoliciesDocumentContentTypes($type = null) {
	$types = array(
		SECURITY_POLICY_USE_CONTENT => __('Use Content'),
		SECURITY_POLICY_USE_ATTACHMENTS => __('Use Attachments'),
		SECURITY_POLICY_USE_URL => __('Use URL')
	);

	if ($type === null) {
		return $types;
	}

	return $types[$type];
}

function getPoliciesDocumentContentTypesWithoutUse($type = null) {
	$types = array(
		SECURITY_POLICY_USE_CONTENT => __('Content'),
		SECURITY_POLICY_USE_ATTACHMENTS => __('Attachments'),
		SECURITY_POLICY_USE_URL => __('URL')
	);

	if ($type === null) {
		return $types;
	}

	return $types[$type];
}

function getNotificationsStatuses($status = null) {
	$statuses = array(
		NOTIFICATION_OBJECT_ENABLED => __('Enabled'),
		NOTIFICATION_OBJECT_DISABLED => __('Disabled')
	);

	if ($status === null) {
		return $statuses;
	}

	return $statuses[$status];
}

function getNotificationsStatusColorClass($status = null) {
	$class = array(
		NOTIFICATION_OBJECT_ENABLED => 'primary',
		NOTIFICATION_OBJECT_DISABLED => 'warning'
	);

	if ($status === null) {
		return $class;
	}

	return $class[$status];
}

function getNotificationsFeedbackStatuses($status = null) {
	$statuses = array(
		NOTIFICATION_FEEDBACK_OK => __('Ok'),
		NOTIFICATION_FEEDBACK_WAITING => __('Notifications sent'),
		NOTIFICATION_FEEDBACK_IGNORE => __('Notifications ignored')
	);

	if ($status === null) {
		return $statuses;
	}

	return $statuses[$status];
}

function getNotificationTypes($type = null) {
	$types = array(
		NOTIFICATION_TYPE_AWARENESS => __('Awareness'),
		NOTIFICATION_TYPE_WARNING => __('Warning'),
		NOTIFICATION_TYPE_DEFAULT => __('Default'),
		NOTIFICATION_TYPE_REPORT => __('Report')
	);

	if (empty($type)) {
		return $types;
	}

	return $types[$type];
}

function getNotificationTypesValues() {
	return array_keys(getNotificationTypes());
}

function getRiskTypes($type = null) {
	$types = array(
		'asset-risk' => __('Asset Risk'),
		'third-party-risk' => __('Third Party Risk'),
		'business-risk' => __('Business Risk')
	);

	if (empty($type)) {
		return $types;
	}

	return $types[$type];
}

function getProgramScopeStatuses($status = null) {
	$statuses = array(
		PROGRAM_SCOPE_DRAFT => __('Draft'),
		PROGRAM_SCOPE_DISCARDED => __('Discarded'),
		PROGRAM_SCOPE_CURRENT => __('Current')
	);

	if (empty($status)) {
		return $statuses;
	}

	return $statuses[$status];
}

function getProgramIssueStatuses($status = null) {
	$statuses = array(
		PROGRAM_ISSUE_DRAFT => __('Draft'),
		PROGRAM_ISSUE_DISCARDED => __('Discarded'),
		PROGRAM_ISSUE_CURRENT => __('Current')
	);

	if (empty($status)) {
		return $statuses;
	}

	return $statuses[$status];
}

function getProgramIssueSources($source = null) {
	$sources = array(
		PROGRAM_ISSUE_INTERNAL => __('Internal'),
		PROGRAM_ISSUE_EXTERNAL => __('External')
	);

	if (empty($source)) {
		return $sources;
	}

	return $sources[$source];
}

function getInternalTypes() {
	return array(
		1 => __('organizational structure'),
		2 => __('roles and responsibilities'),
		3 => __('business strategy and objectives'),
		4 => __('capabilities and resources'),
		5 => __('organizational culture'),
		6 => __('information systems and processes'),
		7 => __('contractual relationships'),
		8 => __('Other')
	);
}

function getExternalTypes() {
	return array(
		21 => __('interested parties'),
		22 => __('political'),
		23 => __('economic'),
		24 => __('cultural'),
		25 => __('technological'),
		26 => __('competitive'),
		27 => __('environment'),
		28 => __('Other')
	);
}

function getTeamRoleStatuses($status = null) {
	$statuses = array(
		TEAM_ROLE_ACTIVE => __('Active'),
		TEAM_ROLE_DISCARDED => __('Inactive')
	);

	if (empty($status)) {
		return $statuses;
	}

	return $statuses[$status];
}

function getGoalStatuses($status = null) {
	$statuses = array(
		GOAL_DRAFT => __('Draft'),
		GOAL_DISCARDED => __('Discarded'),
		GOAL_CURRENT => __('Current')
	);

	if (empty($status)) {
		return $statuses;
	}

	return $statuses[$status];
}

function getIssueStatuses($status = null) {
	$statuses = array(
		ISSUE_OPEN => __('Open'),
		ISSUE_CLOSED => __('Closed'),
	);

	if (empty($status)) {
		return $statuses;
	}

	return $statuses[$status];
}

function parseModelNiceName($model) {
	return trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $model));
}

function getWorstColorType($types = array()) {
	$order = array(
		'success',
		'warning',
		'danger',
		'improvement'
	);

	$currentType = -1;
	$flip = array_flip($order);
	foreach ($types as $type) {
		if ($type === false) {
			continue;
		}
		
		if ($flip[$type] > $currentType) {
			$currentType = $flip[$type];
		}
	}

	if (isset($order[$currentType])) {
		return $order[$currentType];
	}

	return false;
}

function controllerFromModel($model) {
	return Inflector::variable(Inflector::tableize(Inflector::pluralize($model)));
}

/**
 * Allowed for uploading, format extenstion => mimeType.
 */
function getAllowedAttachmentExtensions() {
	return array(
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'ief' => 'image/ief',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png' => 'image/png',
		'x-png' => 'image/png',
		'tiff' => 'image/tiff',
		'psd' => 'image/vnd.adobe.photoshop',
		'dwg' => 'image/vnd.dwg',
		'ico' => 'image/x-icon',
		'pcx' => 'image/x-pcx',
		'pic' => 'image/x-pict',

		'csv' => 'text/csv',
		
		'mp4' => 'video/mp4',
		'mpeg' => 'video/mpeg',
		'ogv' => 'video/ogg',
		'webm' => 'video/webm',
		'f4v' => 'video/x-f4v',
		'avi' => 'video/x-msvideo',

		'doc' => 'application/msword',
		'xls' => 'application/vnd.ms-excel',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'ppt' => 'application/vnd.ms-powerpoint',
		'mpp' => 'application/vnd.ms-project',
		'pdf' => 'application/pdf',

		'odt' => 'application/vnd.oasis.opendocument.text',
		'ott' => 'application/vnd.oasis.opendocument.text-template',
		'oth' => 'application/vnd.oasis.opendocument.text-web',
		'odm' => 'application/vnd.oasis.opendocument.text-master',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'otg' => 'application/vnd.oasis.opendocument.graphics-template',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'otp' => 'application/vnd.oasis.opendocument.presentation-template',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odf' => 'application/vnd.oasis.opendocument.formula',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odi' => 'application/vnd.oasis.opendocument.image',
		'oxt' => 'application/vnd.openofficeorg.extension',

		'zip' => 'application/zip',
		'tar' => 'application/gzip',
		'msg' => 'application/octet-stream',
		'txt' => 'text/plain'
	);
}

// function assetStatus($item) {
// 	$statuses = array();
// 	if ($item['Asset']['expired_reviews'] == RISK_EXPIRED_REVIEWS) {
// 		$statuses[] = array(
// 			'type' => 'warning',
// 			'label' => __('Missing Asset Review')
// 		);
// 	}

// 	return $statuses;
// }

/**
 * Return bool value status of provided query response.
 */
function checkQueryResponse($q) {
	return ((is_array($q) && empty($q)) || $q === true);
}

function getComparisonTypes($labels = false, $dateLabels = false) {
	if ($labels) {
		if (empty($dateLabels)) {
			return array(
				COMPARISON_EQUAL => __('Equal'),
				COMPARISON_ABOVE => __('Above'),
				COMPARISON_UNDER => __('Under')
			);
		}

		return array(
			COMPARISON_EQUAL => __('On'),
			COMPARISON_ABOVE => __('After'),
			COMPARISON_UNDER => __('Before')
		);
	}
	return array(
		COMPARISON_EQUAL => '=',
		COMPARISON_ABOVE => '>',
		COMPARISON_UNDER => '<'
	);
}

function getCustomFieldArg($field) {
	return 'CustomFields_' . $field['slug'];
}

function getComplianceAuditCustomMacros($macro = null) {
	$macros = array(
		COMPLIANCE_AUDIT_MACRO_AUDITCOMPLIANCELIST => __('Compliance Item List'),
		COMPLIANCE_AUDIT_MACRO_LOGINERAMBAURL => __('URL')
	);

	if (empty($macro)) {
		return $macros;
	}

	return $macros[$macro];
}

function getAwarenessProgramCustomMacros($macro = null) {
	$macros = array(
		AWARENESS_PROGRAM_MACRO_UID => __('User UID'),
		AWARENESS_PROGRAM_MACRO_EMAIL => __('User Email'),
		AWARENESS_PROGRAM_MACRO_NAME => __('User Name'),
	);

	if (empty($macro)) {
		return $macros;
	}

	return $macros[$macro];
}

/**
 * @deprecated e1.0.6.045 Use Cron::types() instead
 */
function getCronTypes() {
	App::uses('Cron', 'Model');

	return Cron::types();
}

/**
 * @deprecated e1.0.6.045 Use Cron::statuses() instead
 */
function getCronStatuses() {
	App::uses('Cron', 'Model');

	return Cron::statuses();
}

function getFilterPageLimits() {
	return array(
		15 => 15,
		20 => 20,
		25 => 25,
		30 => 30,
		35 => 35,
		40 => 40,
		45 => 45,
		50 => 50
	);
}

function isExpired($date = null, $status = null) {
	$today = CakeTime::format( 'Y-m-d', CakeTime::fromString( 'now' ) );
	if ( $status !== null ) {
		if ( $date < $today && $status == 1 ) {
			return true;
		}
	}
	else {
		if ( $date < $today ) {
			return true;
		}
	}
	
	return false;
}

function getComplianceAuditCalculatedData($item) {
	$today = CakeTime::format( 'Y-m-d', CakeTime::fromString( 'now' ) );
	$findings_count = 0;
	$assesed_count = 0;
	$total_count = 0;
	foreach ($item['ComplianceFinding'] as $finding) {
		if ($finding['type'] == COMPLIANCE_FINDING_AUDIT) {
			$findings_count++;
		}
		else {
			$assesed_count++;
		}

		$total_count++;
	}

	$open_count = 0;
	$closed_count = 0;
	$expired_count = 0;
	foreach ( $item['ComplianceFinding'] as $finding ) {
		if ( $finding['compliance_finding_status_id'] == COMPLIANCE_FINDING_OPEN ) {
			$open_count++;
		}
		if ( $finding['compliance_finding_status_id'] == COMPLIANCE_FINDING_CLOSED ) {
			$closed_count++;
		}

		$status = 1;
		if ($finding['compliance_finding_status_id'] == COMPLIANCE_FINDING_CLOSED) {
			$status = 0;
		}

		if (isExpired($finding['deadline']) && $finding['compliance_finding_status_id'] == COMPLIANCE_FINDING_OPEN &&  $finding['type'] == COMPLIANCE_FINDING_AUDIT) {
			$expired_count++;
		}
	}

	$settingsCount = count($item['ComplianceAuditSetting']);
	$noEvidenceNeeded = $waitingForEvidence = $evidenceProvided = 0;
	foreach ($item['ComplianceAuditSetting'] as $setting) {
		if ($setting['status'] == COMPLIANCE_AUDIT_STATUS_NOT_EVIDENCE_NEEDED) {
			$noEvidenceNeeded++;
		}
		if ($setting['status'] == COMPLIANCE_AUDIT_STATUS_WAITING_FOR_EVIDENCE) {
			$waitingForEvidence++;
		}
		if ($setting['status'] == COMPLIANCE_AUDIT_STATUS_EVIDENCE_PROVIDED) {
			$evidenceProvided++;
		}
	}

	$assessed_percentage = $open_percentage = $closed_percentage = $expired_percentage = $noEvidence = $waitingEvidence = $providedEvidence = CakeNumber::toPercentage( 0, 2 );

	if ($settingsCount) {
		$noEvidencePrecentage = CakeNumber::toPercentage($noEvidenceNeeded/$settingsCount, 2, array('multiply' => true));
		$waitingEvidencePercentage = CakeNumber::toPercentage($waitingForEvidence/$settingsCount, 2, array('multiply' => true));
		$providedEvidencePercentage = CakeNumber::toPercentage($evidenceProvided/$settingsCount, 2, array('multiply' => true));
	}

	if ( $total_count ) {
		$assessedDistinct = null;
		if (isset($item['ComplianceFindingDistinctAssessed'][0]['ComplianceFindingDistinctAssessed'][0]['count'])) {
			$assessedDistinct = $item['ComplianceFindingDistinctAssessed'][0]['ComplianceFindingDistinctAssessed'][0]['count'];
		}
		
		$assessed_percentage = CakeNumber::toPercentage($assessedDistinct / count($item['ComplianceAuditSetting']), 2, array('multiply' => true));
		$open_percentage = CakeNumber::toPercentage( $open_count / $findings_count, 2, array( 'multiply' => true ) );
		$closed_percentage = CakeNumber::toPercentage( $closed_count / $findings_count, 2, array( 'multiply' => true ) );
		$expired_percentage = CakeNumber::toPercentage( $expired_count / $findings_count, 2, array( 'multiply' => true ) );
	}

	$feedbackSettingsCount = 0;
	$auditeeFeedbacksNotAnswered = 0;
	$auditeeFeedbacks = array();
	foreach ($item['ComplianceAuditSetting'] as $setting) {
		if (!empty($setting['ComplianceAuditFeedbackProfile']['ComplianceAuditFeedback'])) {
			foreach ($setting['ComplianceAuditFeedbackProfile']['ComplianceAuditFeedback'] as $feedback) {
				if (empty($auditeeFeedbacks[$feedback['id']])) {
					$auditeeFeedbacks[$feedback['id']] = array(
						'id' => $feedback['id'],
						'name' => $feedback['name'], 
						'count' => 0,
					);
				}
			}

			foreach ($setting['ComplianceAuditAuditeeFeedback'] as $feedback) {
				$auditeeFeedbacks[$feedback['compliance_audit_feedback_id']]['count']++;
			}

			if (empty($setting['ComplianceAuditAuditeeFeedback'])) {
				$auditeeFeedbacksNotAnswered++;
			}

			$feedbackSettingsCount++;
		}
	}
	if (!empty($auditeeFeedbacks)) {
		foreach ($auditeeFeedbacks as $key => $feedback) {
			$auditeeFeedbacks[$key]['percentage'] = CakeNumber::toPercentage( $feedback['count'] / $feedbackSettingsCount, 2, array( 'multiply' => true ) );
		}
		$auditeeFeedbacks[] = array(
			'name' => __('Not yet answered'),
			'count' => $auditeeFeedbacksNotAnswered,
			'percentage' => CakeNumber::toPercentage( $auditeeFeedbacksNotAnswered / $feedbackSettingsCount, 2, array( 'multiply' => true ) )
		);
	}

	return array(
		'findings_count' => $findings_count,
		'assesed_count' => $assesed_count,
		'assessed_percentage' => $assessed_percentage,
		'open_percentage' => $open_percentage,
		'open_count' => $open_count,
		'closed_percentage' => $closed_percentage,
		'closed_count' => $closed_count,
		'expired_percentage' => $expired_percentage,
		'expired_count' => $expired_count,
		'noEvidencePrecentage' => $noEvidencePrecentage,
		'noEvidenceNeeded' => $noEvidenceNeeded,
		'waitingEvidencePercentage' => $waitingEvidencePercentage,
		'waitingForEvidence' => $waitingForEvidence,
		'providedEvidencePercentage' => $providedEvidencePercentage,
		'evidenceProvided' => $evidenceProvided,
		'auditeeFeedbacks' => $auditeeFeedbacks,
	);
}

function filterComplianceData($data) {
	foreach ($data as $key => $entry) {
		$hasItems = false;
		foreach ( $entry['CompliancePackage'] as $compliance_package ) {
			if ( ! $hasItems && ! empty( $compliance_package['CompliancePackageItem'] ) ) {
				$hasItems = true;
			}
		}

		if ( ! $hasItems ) {
			unset($data[$key]);
		}
	}

	return $data;
}

function getPercentageOptions($multiplier = 10) {
	$percentages = array();
	for ( $i = 0; $i <= $multiplier; $i++ ) {
		$val = $i * 10;
		$percentages[ $val ] = CakeNumber::toPercentage( $val, 0 );
	}

	return $percentages;
}

function getReversePercentageOptions($multiplier = 10) {
	return array_reverse(getPercentageOptions($multiplier), true);
}

function traverser($Item, $FilterField) {
	$fieldName = $FilterField->getFieldName();
	$fieldData = $FilterField->getFieldDataConfig();

	if ($fieldData !== null) {
		$traverse = explode('.', $fieldData);
		$fieldName = array_pop($traverse);

		if (!empty($traverse)) {
			foreach ($traverse as $where) {
				if (!$Item->{$where}) {
					// trigger_error(sprintf('Traverser pointer "%s" does not exist!', $where));
					return false;
				}

				$Item = $Item->{$where};
			}
		}
	}

	return [
		'ItemDataEntity' => $Item,
		'FieldDataEntity' => $FilterField->getFieldDataObject()
	];
}

function addArrayValues(&$array1, $array2)
{
	$array2 = array_values($array2);

	$i = 0;
	foreach ($array1 as $key => $value) {
		$array1[$key] += $array2[$i];
		$i++;
	}

	return $array1;
}
