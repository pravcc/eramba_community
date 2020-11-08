<?php
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');

// Awareness Program Users KPI object
class AwarenessProgramKpi extends DashboardKpiObject {

	public function __construct(Dashboard $Dashboard, $data) {
		parent::__construct($Dashboard, $data);

		$this->resultQuery = [
		];
	}

	/**
	 * Awarenes program will always have 2 attributes, one is program ID and the other is user model type,
	 * to make query on.
	 */
	protected function _buildResultQuery() {
		$attributes = [];
		foreach ($this->_data['DashboardKpiAttribute'] as $attribute) {
			$className = $attribute['model'];
			$attribute = $attribute['foreign_key'];

			$attributes[$className] = $attribute;
		}
		
		if (!isset($attributes['AwarenessProgram']) || !isset($attributes['AwarenessProgramUserModel'])) {
			throw new DashboardException('Attributes required to calculate Awareness Program Users count are missing.', 1);
		}

		$params = [
			'awareness_program_id' => [
				'value' => $attributes['AwarenessProgram'],
				'comparisonType' => 0
			]
		];

		$AwarenessProgramUserModel = ClassRegistry::init('AwarenessProgram' . $attributes['AwarenessProgramUserModel']);

		$_filter = new AdvancedFiltersObject();
		$_filter->setModel($AwarenessProgramUserModel);
		$_filter->setFilterValues([]);
		$_filter->setConvertedValues($params);
		$conditions = $_filter->getConditions();

		$this->resultQuery = [
			'conditions' => $conditions,
			'fields' => [$AwarenessProgramUserModel->escapeField('id')]
		];

		$this->Model = $AwarenessProgramUserModel;
	}

}
