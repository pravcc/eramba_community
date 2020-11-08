<?php
class ComplianceAuditsHelper extends AppHelper {
	public $helpers = array('Html', 'AdvancedFilters', 'Eramba');
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->Eramba->processItemArray($item, 'ComplianceAudit');
		$statuses = array();

		if ($this->Eramba->getAllowCond($allow, 'status')) {
			if ($item['ComplianceAudit']['status'] == COMPLIANCE_AUDIT_STARTED) {
				$statuses[$this->Eramba->getStatusKey('status')] = array(
					'label' => ComplianceAudit::statuses(COMPLIANCE_AUDIT_STARTED),
					'type' => 'success'
				);
			}

			if ($item['ComplianceAudit']['status'] == COMPLIANCE_AUDIT_STOPPED) {
				$statuses[$this->Eramba->getStatusKey('status')] = array(
					'label' => ComplianceAudit::statuses(COMPLIANCE_AUDIT_STOPPED),
					'type' => 'danger'
				);
			}
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->Eramba->processStatusOptions($options);

		$statuses = $this->getStatusArr($item, $options['allow']);
		return $this->Eramba->styleStatuses($statuses, $options);
	}

	/**
	 * Returns status labels.
	 * 
	 */
	public function getStatusLabels($item) {
		if (empty($item) || empty($item['status'])) {
			return array();
		}

		$msg = array();

		$label = getComplianceAuditSettingStatuses($item['status'], 'labelText');
		$class = getComplianceAuditSettingStatuses($item['status'], 'labelClass');

		$msg[] = $this->Html->tag('span', $label, array('class' => 'label ' . $class));

		return $msg;
	}

	public function statusLabels($item, $implodeGlue = '<br>') {
		$labels = $this->getStatusLabels($item);

		echo implode($implodeGlue, $labels);
	}

	/**
	 * @var $item Compliance Audit item.
	 */
	public function getFindingsWithAssociations($findings) {
		$list = array();
		foreach ($findings as $finding) {
			if (!empty($finding['ComplianceException'])) {
				foreach ($finding['ComplianceException'] as $complianceException) {
					$list[] = $this->getFindingAssociatedLabel(
						$finding['title'],
						__('Compliance Exception'),
						$complianceException['title']
					);
				}
			}

			if (!empty($finding['ThirdPartyRisk'])) {
				foreach ($finding['ThirdPartyRisk'] as $thirdPartyRisk) {
					$list[] = $this->getFindingAssociatedLabel(
						$finding['title'],
						__('Third Party Risk'),
						$thirdPartyRisk['title']
					);
				}
			}
		}

		return $list;
	}

	private function getFindingAssociatedLabel($findingTitle, $section, $itemTitle) {
		return sprintf(
			'%s: %s (%s)',
			$findingTitle,
			$section,
			$itemTitle
		);
	}

	/**
	 * Filter output of Compliance Package Item IDs into the Advanced Filter.
	 */
	public function outputItemIds($data, $options = array()) {
		return $this->outputRedirect($data, $options);
	}

	/**
	 * Filter output of Compliance Package Statuses into the Advanced Filter.
	 */
	public function outputStatuses($data, $options = array()) {
		return $this->outputRedirect($data, $options);
	}

	/**
	 * Filter output of Compliance Package Item Names into the Advanced Filter.
	 */
	public function outputItemNames($data, $options = array()) {
		return $this->outputRedirect($data, $options);
	}

	/**
	 * Make output edirect btn
	 */
	protected function outputRedirect($data, $options) {
		$instance = $options['AdvancedFiltersDataInstance'];

		// if ($instance->getViewType() == AdvancedFiltersData::VIEW_TYPE_HTML) {
			if (!empty($data['ComplianceAuditSetting'])) {
				$link = $this->AdvancedFilters->getItemFilteredLink(__('List Items'), 'ComplianceAuditSetting', $data['ComplianceAudit']['id'], array(
					'key' => 'compliance_audit_id'
				), $options);
				return $link;
			}
		// }

		return getEmptyValue(false);
	}

	/**
	 * Filter output of Compliance Findings into the Advanced Filter.
	 */
	public function outputFindingsLink($data, $options = array()) {
		$link = $this->AdvancedFilters->getItemFilteredLink(__('List Findings'), 'ComplianceFinding', $data, array(
			'key' => 'compliance_audit_id'
		), $options);
		return $link;
	}

	/**
	 * Filter output of Compliance Audit Settings into the Advanced Filter.
	 */
	public function outputSettingsLink($data, $options = array()) {
		$link = $this->AdvancedFilters->getItemFilteredLink(__('List Settings'), 'ComplianceAuditSetting', $data, array(
			'key' => 'compliance_audit_id'
		), $options);
		return $link;
	}

	/**
	 * inflects item according to count
	 */
	public function itemsI18n($numberOfItems) {
		return sprintf(__n('%d item', '%d items', $numberOfItems), $numberOfItems);
	}

	public function getAnalyzeLink($auditId) {
		return $this->Html->link(__('Link'), $this->getAnalyzeUrl($auditId));
	}

	public function getAnalyzeUrl($auditId) {
		return [
            'plugin' => 'thirdPartyAudits',
            'controller' => 'thirdPartyAudits',
            'action' => 'analyze',
            $auditId
        ];
	}

}