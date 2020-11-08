<?php
App::uses('DashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');
App::uses('CompliancePackage', 'Model');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class ComplianceManagementDashboardAttribute extends DashboardAttribute {

	protected $_storeAttributes = null;

	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		parent::__construct($Dashboard, $DashboardKpiObject);

		$this->templates = [
		];

		$this->labels = ClassRegistry::init('CompliancePackageRegulator')->find('list');
	}

	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		$query['CompliancePackage-compliance_package_regulator_id'] = $attribute;

		return $query;
	}

	public function listAttributes(Model $Model) {
		if ($this->_storeAttributes === null) {
			$data = ClassRegistry::init('CompliancePackageRegulator')->find('list', [
				'fields' => ['CompliancePackageRegulator.id'],
				'recursive' => -1
			]);

			$this->_storeAttributes = $data;
		}
		
		return $this->_storeAttributes;
	}

	public function joinAttributes(Model $Model) {
		$joins = $Model->thirdPartyJoins;

		array_unshift($joins, [
			'table' => 'compliance_package_items',
			'alias' => 'CompliancePackageItem',
			'type' => 'INNER',
			'conditions' => [
				'CompliancePackageItem.id = ComplianceManagement.compliance_package_item_id'
			]
		]);

		return $joins;
	}

	public function applyAttributes(Model $Model, $attribute) {
		return [
			'CompliancePackageRegulator.id' => $attribute
		];
	}

	public function buildQuery(Model $Model, $attribute) {
		return [
			'joins' => $this->joinAttributes($Model),
			'conditions' => $this->applyAttributes($Model, $attribute),
			'fields' => [$Model->escapeField($Model->primaryKey)]
		];
	}

	public function getLabel(Model $Model, $attribute) {
		return __('Compliance Package: %s', $this->labels[$attribute]);
	}
}