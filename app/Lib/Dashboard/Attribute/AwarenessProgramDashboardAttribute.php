<?php
App::uses('DashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');

class AwarenessProgramDashboardAttribute extends DashboardAttribute {

	protected $_storeAttributes = null;

	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		parent::__construct($Dashboard, $DashboardKpiObject);

		$this->templates = [];
	}

	public function getLabel(Model $Model, $attribute) {
		return __('AwarenessProgram: %s', $Model->getRecordTitle($attribute));
	}

	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		$query['awareness_program_id'] = $attribute;

		return $query;
	}

	public function listAttributes(Model $Model) {
		if ($this->_storeAttributes === null) {
			$data = $Model->find('list', [
				'fields' => ['AwarenessProgram.id'],
				'recursive' => -1
			]);

			$this->_storeAttributes = $data;
		}
		
		return $this->_storeAttributes;
	}

	public function buildQuery(Model $Model, $attribute) {
		return [];
	}
}