<?php
App::uses('AppHelper', 'View/Helper');
App::uses('ComplianceAnalysisFinding', 'Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class ComplianceAnalysisFindingsHelper extends AppHelper {
	public $settings = array();
	public $helpers = ['PolicyExceptions', 'Users', 'Html', 'Ux', 'Taggable', 'Eramba', 'FieldData.FieldData', 'FormReload'];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function compliancePackageRegulatorField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}

	public function compliancePackageItemField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'options' => $this->_View->get('compliancePackageItemsCustom')
		]);
	}

	public function getStatusArr($item, $allow = '*', $model = 'ComplianceAnalysisFinding') {
		$item = $this->Eramba->processItemArray($item, $model);
		$statuses = array();

		if ($this->Eramba->getAllowCond($allow, 'status') && $item[$model]['status'] == ComplianceAnalysisFinding::STATUS_CLOSED) {
			$statuses[$this->Eramba->getStatusKey('status')] = array(
				'label' => __('Closed'),
				'type' => 'success'
			);
		}
		
		if ($this->Eramba->getAllowCond($allow, 'expired') && $item[$model]['expired'] == ITEM_STATUS_EXPIRED) {
			$statuses[$this->Eramba->getStatusKey('expired')] = array(
				'label' => __('Expired'),
				'type' => 'danger'
			);
		}
		else {
			if ($this->Eramba->getAllowCond($allow, 'status') && $item[$model]['status'] == ComplianceAnalysisFinding::STATUS_OPEN) {
				$statuses[$this->Eramba->getStatusKey('status')] = array(
					'label' => __('Open'),
					'type' => 'success'
				);
			}
		}

		return $statuses;
	}

	public function getStatuses($item, $model = 'ComplianceAnalysisFinding', $options = array()) {
		$options = $this->Eramba->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow'], $model);

		return $this->Eramba->styleStatuses($statuses, $options);
	}

	public function getDueDate($item) {
		return $this->Ux->date($item['ComplianceAnalysisFinding']['due_date']);
	}

	public function getOwners($item) {
		return $this->Users->listNames($item, 'Owner');
	}

	public function getCollaborators($item) {
		return $this->Users->listNames($item, 'Collaborator');
	}

	public function getDescription($item) {
		return $this->Ux->text($item['ComplianceAnalysisFinding']['description']);
	}

	public function getTags($item) {
		return $this->Taggable->showList($item, [
			'notFoundCallback' => [$this->Taggable, 'notFoundBlank']
		]);
	}

	/**
	 * @deprecated
	 */
	public function getAssociatedData($item) {
		$headings = [
			__('Compliance Package Name'),
			__('Compliance Package ID'),
			__('Compliance Package Title'),
			__('Compliance Package Description')
		];

		$commonComplianceData = $this->_View->viewVars['commonComplianceData'];
		$data = [];
		if (!empty($item['ComplianceManagement'])) {
			foreach ($item['ComplianceManagement'] as $complianceItem) {
				$packageItem = $commonComplianceData[$complianceItem['id']];

				$data[] = [
					$packageItem['ThirdParty']['name'],
					$packageItem['CompliancePackageItem']['item_id'],
					$packageItem['CompliancePackageItem']['name'],
					$packageItem['CompliancePackageItem']['description']
				];
			}
		}

		return [
			'titles' => $headings,
			'data' => $data
		];
	}

}
