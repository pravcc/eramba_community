<?php
App::uses('DashboardException', 'Dashboard.Error');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('CakeLog', 'Log');

// Dashboard KPI object
class DashboardKpiObject {

	/**
	 * Dashboard Instance.
	 * 
	 * @var null|Dashboard
	 */
	public $Dashboard = null;

	/**
	 * DashboardKpi Instance.
	 * 
	 * @var null|DashboardKpi
	 */
	public $DashboardKpi = null;

	/**
	 * Model Instance.
	 * 
	 * @var null|Model
	 */
	public $Model = null;

	/**
	 * Title.
	 * 
	 * @var null|string
	 */
	public $title = null;

	/**
	 * Category of this KPI.
	 * 
	 * @var null|int
	 */
	public $category = null;

	/**
	 * Type for this KPI. Either user or admin type is available.
	 * 
	 * @var null|int
	 */
	public $type = null;

	/**
	 * Attributes for this KPI.
	 * Array of DashboardAttribute objects.
	 * 
	 * @var null|array
	 */
	public $attributes = null;

	public $resultQuery = null;

	protected $_data = null;
	
	/**
	 * Initialize the class.
	 */
	public function __construct(Dashboard $Dashboard, $data) {
		$this->Dashboard = $Dashboard;
		$this->_data = $data;

		$this->DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');

		$Model = ClassRegistry::init($data['DashboardKpi']['model']);
		$this->Model = $Model;

		$this->primaryField = $Model->escapeField($Model->primaryKey);

		$this->resultQuery = [
			'conditions' => [],
			// we disable soft delete generally for the final query that inludes attributes subqueries,
			// which already have conditional parameters for soft delete managed themselves
			'softDelete' => false,
			'group' => [$this->primaryField],
			'recursive' => -1
		];
	}

	protected function _buildResultQuery() {
		foreach ($this->_data['DashboardKpiAttribute'] as $attribute) {
			$className = $attribute['model'];
			$attribute = $attribute['foreign_key'];
			
			$this->Dashboard->resetAttributeInstance($className);
			
			// retrieve the attribute class
			$AttributeInstance = $this->Dashboard->attributeInstance($className, $this);

			$subqueryParams = $AttributeInstance->buildQuery($this->Model, $attribute);
			if ($subqueryParams === false) {
				// CakeLog::write(LOG_DEBUG, sprintf('Dashboard attribute at %s - %s.%s returns false instead of query.', $this->Model->alias, $className, $attribute));
				continue;
			}
			
			$subqueryParams['softDelete'] = $AttributeInstance->softDelete($this->Model, $attribute);
			$subqueryParams['recursive'] = -1;
			
			// lets force groupping for this query to avoid duplicated resultset
			if (!isset($subqueryParams['group'])) {
				$subqueryParams['group'] = [$this->primaryField];
			}

			// specifically for advanced filters
			if ($className == 'AdvancedFilter') {
				$_filter = new AdvancedFiltersObject($attribute);
				$_filter->getConditions();
				$this->resultQuery = array_merge($this->resultQuery, $_filter->parseFindOptions());
			}

			// this builds the main query using this attribute
			$query = $this->Model->getQuery('all', $subqueryParams);
			$this->resultQuery['conditions'][] = "{$this->primaryField} IN ({$query})";
		}
	}

	/**
	 * Gets the current attribute list for the kpi.
	 */
	public function getAttributeList() {
		$attributesList = Hash::combine($this->_data['DashboardKpiAttribute'], '{n}.model', '{n}.foreign_key');

		return $attributesList;
	}

	protected function _calculate() {
		return (int) $this->Model->find('count', $this->resultQuery);
	}

	public function calculate() {
		try {
			if ($this->_buildResultQuery() === false) {
				// log it only
			}
			else {
				// we get the final result, forcing integer type as groupped count query returns false instead of zero
				return $this->_calculate();
			}
		}
		// handle possible query issues
		catch (CakeException $e) {
			$message = "Dashboard re-calculation failed with exception: " . $e->getMessage() . "\n\r";
			$message .= "Query: " . print_r($this->resultQuery, true);

			throw new DashboardException($message);
		}
	}
}
