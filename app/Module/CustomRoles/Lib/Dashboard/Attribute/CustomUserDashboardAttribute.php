<?php
App::uses('DashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');

class CustomUserDashboardAttribute extends DashboardAttribute {
	public $attributeColumn = 'id';
	protected $_storeAttributes = null;

	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		parent::__construct($Dashboard, $DashboardKpiObject);

		$listAttributes = $this->listAttributes();

		// check if the custom role user exists, because if a user was deleted previously, we need to delete it
		// also for dashboards synchronization so it doesnt sync broken relationship
		$data = ClassRegistry::init('Dashboard.DashboardKpiAttribute')->find('list', [
			'conditions' => [
				'DashboardKpiAttribute.model' => 'CustomRoles.CustomUser',
				'DashboardKpiAttribute.foreign_key !=' => $listAttributes
			],
			'fields' => ['DashboardKpiAttribute.kpi_id'],
			'recursive' => 0
		]);

		if (!empty($data)) {
			ClassRegistry::init('Dashboard.DashboardKpi')->deleteAll([
				'DashboardKpi.id' => $data
			]);
		}
	}

	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		if (!isset($item['attributes']['CustomRoles.CustomRole'])) {
			throw new DashboardException("Error occured when building URL for filtering Custom User '{$attribute}'. There is no Custom Role defined as KPI attribute, we are not able to build the url without it.", 1);
		}

		$filterField = $this->mapFilterField($Model, $item['attributes']['CustomRoles.CustomRole']);
		$attribute = 'User-' . $attribute;
		$query[$filterField] = $attribute;
		
		return $query;
	}

	public function listAttributes(Model $Model = null) {
		if ($this->_storeAttributes === null) {
			$this->_storeAttributes = ClassRegistry::init('CustomRoles.CustomRolesUser')->find('list', [
				'fields' => ['CustomRolesUser.user_id'],
				'recursive' => -1
			]);
		}
		
		return $this->_storeAttributes;
	}

	public function joinAttributes(Model $Model) {
		return [
			[
				// @todo wrong aliasing
				'table' => 'custom_roles_role_users',
				'alias' => 'CustomRolesUsers',
				'type' => 'INNER',
				'conditions' => [
					'CustomRolesUsers.model' => $Model->alias,
					'CustomRolesUsers.foreign_key = ' . $Model->escapeField($Model->primaryKey)
				]	
			],
			[
				'table' => 'custom_roles_users',
				'alias' => 'CustomRolesUser',
				'type' => 'INNER',
				'conditions' => [
					'CustomRolesUser.user_id = CustomRolesUsers.user_id'
				]	
			],
			[
				'table' => 'custom_roles_roles',
				'alias' => 'CustomRolesRole',
				'type' => 'INNER',
				'conditions' => [
					'CustomRolesRole.id = CustomRolesUsers.custom_roles_role_id'
				]	
			],
		];
	}

	public function applyAttributes(Model $Model, $attribute) {
		$attributeList = $this->DashboardKpiObject->getAttributeList();
		$conds = [
			'CustomRolesUser.user_id' => $attribute
		];

		if (isset($attributeList['CustomRoles.CustomRole'])) {
			$conds['CustomRolesRole.field'] = $attributeList['CustomRoles.CustomRole'];
		}
		
		return $conds;
	}

	public function buildQuery(Model $Model, $attribute) {
		$conditions = $this->applyAttributes($Model, $attribute);

		return [
			'joins' => $this->joinAttributes($Model),
			'conditions' => $conditions,
			'fields' => [$Model->escapeField($Model->primaryKey)]
		];
	}
}