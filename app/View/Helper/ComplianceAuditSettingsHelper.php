<?php
class ComplianceAuditSettingsHelper extends AppHelper {
	public $helpers = array('Html', 'AdvancedFilters');
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function outputAnswers($data, $options = array()) {
		$link = $this->AdvancedFilters->getItemFilteredLink(__('List Answers'), 'ComplianceAuditAuditeeFeedback', null, array(
			'query' => array(
				// 'compliance_audit_setting_id' => $data['ComplianceAuditSetting']['id'],
				'compliance_package_item_item_id' => $data['CompliancePackageItem']['item_id'],
				'compliance_audit_feedback_profile_id' => $data['ComplianceAuditSetting']['compliance_audit_feedback_profile_id']
			)
		), $options);
		return $link;
	}

	public function outputFindingsLink($data, $options = array()) {
		$link = $this->AdvancedFilters->getItemFilteredLink(__('List Findings'), 'ComplianceFinding', null, array(
			'query' => array(
				'compliance_audit_id' => $data['ComplianceAudit']['id'],
				'compliance_package_item_item_id' => $data['CompliancePackageItem']['item_id'],
				// 'compliance_audit_feedback_profile_id' => $data['ComplianceAuditSetting']['compliance_audit_feedback_profile_id']
			)
		), $options);
		return $link;
	}
}