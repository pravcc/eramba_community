<?php
App::uses('DashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');

class CustomRoleDashboardAttribute extends DashboardAttribute {

	/**
	 * Builds the part of the query for url that manages to filter using a given custom role.
	 * @param  Model  $Model     Model.
	 * @param  array  &$query    Query that hold parameters for URL.
	 * @param  string $attribute Attribute value.
	 * @param  array  $item      The entire item data.
	 * @return array             Returns the modified $query.
	 */
	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		return $query;
	}

	public function listAttributes(Model $Model) {
		if ($Model->Behaviors->loaded('CustomRoles.CustomRoles')) {
			return $Model->Behaviors->CustomRoles->getModelSettings($Model);
		}

		return [];
	}

	public function joinAttributes(Model $Model) {
		return [];
		// return [
		// 	[
		// 		'table' => 'custom_roles_role_users',
		// 		'alias' => 'CustomRolesUsers',
		// 		'type' => 'INNER',
		// 		'conditions' => [	
		// 			'CustomRolesUsers.model' => $Model->alias,
		// 			'CustomRolesUsers.foreign_key = ' . $Model->escapeField($Model->primaryKey)
		// 		]	
		// 	],
		// 	[
		// 		'table' => 'custom_roles_roles',
		// 		'alias' => 'CustomRolesRole',
		// 		'type' => 'INNER',
		// 		'conditions' => [
		// 			'CustomRolesRole.id = CustomRolesUsers.custom_roles_role_id'
		// 		]	
		// 	],
		// ];
	}

	public function applyAttributes(Model $Model, $attribute) {
		// $conds = [
		// 	'CustomRolesRole.field' => $attribute
		// ];

		// return $conds;
	}

	public function buildQuery(Model $Model, $attribute) {
		return false;
		// return [
		// 	'joins' => $this->joinAttributes($Model),
		// 	'conditions' => $this->applyAttributes($Model, $attribute),
		// 	'fields' => [$Model->escapeField($Model->primaryKey)]
		// ];
	}
}