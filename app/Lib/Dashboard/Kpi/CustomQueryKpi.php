<?php
// Dashboard KPI object
class CustomQueryKpi extends DashboardKpiObject {

	public function __construct(Dashboard $Dashboard, $data) {
		parent::__construct($Dashboard, $data);

		$this->resultQuery = [
			'conditions' => [],
			// we disable soft delete generally for the final query that inludes attributes subqueries,
			// which already have conditional parameters for soft delete managed themselves
			'softDelete' => false,
			// 'group' => [$this->primaryField],
			'recursive' => -1
		];
	}

	protected function _buildResultQuery() {
		foreach ($this->_data['DashboardKpiAttribute'] as $attribute) {
			$className = $attribute['model'];
			$attribute = $attribute['foreign_key'];

			// retrieve the attribute class
			$AttributeInstance = $this->Dashboard->attributeInstance($className);

			$subqueryParams = $AttributeInstance->buildQuery($this->Model, $attribute);
			$subqueryParams['softDelete'] = $AttributeInstance->softDelete($this->Model, $attribute);
			$subqueryParams['recursive'] = -1;

			$this->resultQuery = array_merge($this->resultQuery, $subqueryParams);
		}

		return true;
	}

	/**
	 * Calculate a custom attributes query.
	 */
	protected function _calculate() {
		$result = $this->Model->find('all', $this->resultQuery);

		$reset = reset($result);
		$reset = reset($reset);
		$reset = reset($reset);

		return (int) $reset;
	}

}
