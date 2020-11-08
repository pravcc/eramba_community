<?php
App::uses('ImportToolBase', 'ImportTool.Lib');

class ImportToolImport extends ImportToolBase {
	protected $ImportToolData = null;
	protected $_importRows = array();
	protected $_importSpecificRows = false;

	public function __construct(ImportToolData $ImportToolData) {
		$this->ImportToolData = $ImportToolData;
		parent::__construct($this->ImportToolData->_getModelName());
	}

	/**
	 * Return current ImportToolData class instance for the current import.
	 * 
	 * @return ImportToolData
	 */
	public function getImportToolData()
	{
		return $this->ImportToolData;
	}

	public function getImportSpecificRows()
	{
		return $this->_importSpecificRows;
	}

	public function getImportRows()
	{
		return $this->_importRows;
	}

	/**
	 * Set variable for importing just specific rows.
	 * 
	 * @param array $rows
	 */
	public function setImportRows($rows = array()) {
		if (!empty($rows)) {
			$this->_importRows = $rows;
			$this->_importSpecificRows = true;
		}
	}

	public function saveData($additionalItemData = [], $afterSaveCallable = null) {
		$data = $this->ImportToolData->getImportableDataArray();

		$ret = true;
		foreach ($data as $row => $item) {
			if (!$this->_importSpecificRows || ($this->_importSpecificRows && in_array($row, $this->_importRows))) {
				$item = Hash::merge($item, $additionalItemData);

				if ($this->_getModel()->alias == 'SecurityService') {
					$item = $this->_updateFieldsWhenDesign($item);
					$item = $this->_setAuditsMaintenances(null, $item);
				}

				// ddd($item);

				$this->_getModel()->create();
				$itemRet = $this->_getModel()->saveAssociated($item, array(
					'autocommit' => true,
					'validate' => false
					// 'fieldList' => ...
				));

				$ret &= $itemRet;
				
				if ($afterSaveCallable !== null) {
					call_user_func($afterSaveCallable, $itemRet, $item, $row);
				}

				//if there is ObjecStatus trigger calculations
				if ($this->_getModel()->Behaviors->enabled('ObjectStatus')) {
					$this->_getModel()->triggerObjectStatus();
				}
			}
		}

		return $ret;
	}

	protected function _updateFieldsWhenDesign($data)
	{
		$auditsText = __('The control is in design. Audits not possible.');
		$maintenancesText = __('The control is in design. Maintenances not possible.');

		if (isset($data['SecurityService']['security_service_type_id'])
			&& $data['SecurityService']['security_service_type_id'] == SECURITY_SERVICE_DESIGN
		) {
			$text = $data['SecurityService']['audit_metric_description'];
			$pos = strpos($text, $auditsText);

			if ($pos === false) {
				$data['SecurityService']['audit_metric_description'] .= ' ' . $auditsText;
			}

			$text = $data['SecurityService']['audit_success_criteria'];
			$pos = strpos($text, $auditsText);

			if ($pos === false) {
				$data['SecurityService']['audit_success_criteria'] .= ' ' . $auditsText;
			}

			$text = $data['SecurityService']['maintenance_metric_description'];
			$pos = strpos($text, $maintenancesText);

			if ($pos === false) {
				$data['SecurityService']['maintenance_metric_description'] .= ' ' . $maintenancesText;
			}
		}

		return $data;
	}

	protected function _setAuditsMaintenances($id = null, $requestData)
	{
		// $requestData = $this->request->data;
		$models = [
			'SecurityServiceAudit',
			'SecurityServiceMaintenance'
		];

		foreach ($models as $model) {
			$data = [];

			// i.e SecurityServiceAuditDate
			$dateModel = $model . 'Date';

			// if date model is not part of submitted data, skip
			if (!isset($requestData[$dateModel]) || empty($requestData[$dateModel])) {
				continue;
			}

			// cycle all dates for a date model
			foreach ($requestData[$dateModel] as $date) {
				$formattedDate = date_create(date('Y') . '-' . $date['month'] . '-' . $date['day']);
				$formattedDate = date_format($formattedDate, 'Y-m-d');

				$_data = [
					'planned_date' => $formattedDate
				];

				// configure correct data
				if ($model == 'SecurityServiceAudit') {
					$_data = $_data + [
						'AuditOwner' => $requestData['SecurityService']['AuditOwner'],
						'AuditEvidenceOwner' => $requestData['SecurityService']['AuditEvidenceOwner'],
						'audit_metric_description' => $requestData['SecurityService']['audit_metric_description'],
						'audit_success_criteria' => $requestData['SecurityService']['audit_success_criteria'],
					];
				}

				if ($model == 'SecurityServiceMaintenance') {
					$_data = $_data + [
						'MaintenanceOwner' => $requestData['SecurityService']['MaintenanceOwner'],
						'task' => $requestData['SecurityService']['maintenance_metric_description']
					];
				}

				$dataItem = [
					$model => $_data
				];

				if ($id !== null) {
					// check for the same audit/maintenance to no create duplicates
					$secServData = $this->SecurityService->{$model}->find('all', array(
						'fields' => array(
							$model . '.id',
							$model . '.planned_date'
						),
						'conditions' => array(
							$model . '.security_service_id' => $id,
							$model . '.planned_date' => $formattedDate
						),
						'recursive' => -1
					));

					if (!empty($secServData)) {
						continue;
					}
				}

				$data[] = $dataItem;
			}

			$requestData[$model] = $data;
		}

		return $requestData;
	}

}
