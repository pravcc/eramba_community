<?php
App::uses('CustomRolesAppModel', 'CustomRoles.Model');
App::uses('CustomRoles', 'CustomRoles.Lib');

class CustomRolesRole extends CustomRolesAppModel {
	public $useTable = 'roles';
	public $displayField = 'field';

	public function attributeQuery($options) {
		$conds = [];
		foreach ($options['CustomRoles.CustomRoleRole'] as $role) {
			foreach ($options['CustomRoles.CustomRolesUser'] as $user) {
				$conds[] = [
					'CustomRolesUsers.custom_roles_role_id' => $role,
					'CustomRolesUsers.user_id' => $user
				];
			}
		}

		return [
			'conditions' => $conds,
			'joins' => [
				[
					'alias' => 'sadasd'
				]
			]
		];
	}
}
