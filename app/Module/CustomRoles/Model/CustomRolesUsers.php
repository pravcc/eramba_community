<?php
App::uses('CustomRolesAppModel', 'CustomRoles.Model');
App::uses('Hash', 'Utility');
App::uses('CakeLog', 'Log');

class CustomRolesUsers extends CustomRolesAppModel {
	public $useTable = 'role_users';

	public $belongsTo = [
		'CustomRolesRole' => [
			'className' => 'CustomRoles.CustomRolesRole',
			'foreignKey' => 'custom_roles_role_id'
		],
		'User'
	];

	/**
	 * Add permission to it's relevant object.
	 */
	public function afterSave($created, $options = array()) {
		$ret = true;

		$data = $this->data['CustomRolesUsers'];
		if (ClassRegistry::init($data['model'])->Behaviors->enabled('Visualisation.Visualisation')) {
			$CustomRolesUser = ClassRegistry::init('CustomRoles.CustomRolesUser');
			$ret &= $CustomRolesUser->setPermission('allow', $this->field('user_id'), $data['model'], $data['foreign_key']);
		}

		return $ret;
	}

	public function beforeDelete($cascade = true) {
		$ret = true;

		if (ClassRegistry::init($this->field('model'))->Behaviors->enabled('Visualisation.Visualisation')) {
			$CustomRolesUser = ClassRegistry::init('CustomRoles.CustomRolesUser');
			$ret &= $CustomRolesUser->setPermission('inherit', $this->field('user_id'), $this->field('model'), $this->field('foreign_key'));
		}
		

		return $ret;
	}

	// compatible method for older custom roles
	public function parseCustomCompatible($model, $roles, $objects = []) {
		$Model = ClassRegistry::init($model);
		$roles = Hash::extract($roles, '{n}.custom_identifier');

		// formatted ['notification_system_item_object_id' => 'object_foreign_key']
		$objects = Hash::combine($objects, '{n}.id', '{n}.foreign_key');

		$condsList = [];
		if (!empty($roles)) {
			foreach ($roles as $role) {
				list($roleModel, $field) = explode('.', $role);
				$condsList[$roleModel][] = $field;
			}
		}

		$conds = ['OR' => []];
		$objectList = [];
		foreach ($condsList as $roleModel => $fields) {
			$foreignKey = [];

			// for each object we need to determine the query on db and relations to get custom role users
			foreach ($objects as $primaryId => $objectId) {
				$relatedId = $objectId;

				//plugin split to get only model name to prevent inconsistent model name using
				list($roleModelPlugin, $roleModelName) = pluginSplit($roleModel);
				list($modelPlugin, $modelName) = pluginSplit($model);

				// custom role is fetched from parent object (only possible case at the moment when models do not match)
				// @todo make this dynamic in the future when needed
				if ($roleModelName != $modelName) {
					$Model->id = $objectId;
					$parent = $Model->parentNode('Aco');

					if ($parent !== null) {
						$relatedId = reset($parent)['id'];
					}
					// no parent found, just skip it
					else {
						CakeLog::write('error', 'Custom Roles could not be determined for roleModel %s in model %s ID %d', $roleModel, $Model->alias, $objectId);
						continue;
					}
				}

				$objectList[$primaryId][] = [
					'model' => $roleModel,
					'id' => $relatedId
				];

				$foreignKey[] = $relatedId;
			}

			$conds['OR'][] = [
				$this->escapeField('model') => $roleModel,
				$this->CustomRolesRole->escapeField('field') => $fields,
				$this->escapeField('foreign_key') => $foreignKey
			];
		}

		$data = $this->find('all', [
			'conditions' => $conds,
			'fields' => [
				'CustomRolesUsers.*'
			],
			'recursive' => 0
		]);
		$data = Hash::extract($data, '{n}.CustomRolesUsers');
		
		$final = [];
		foreach ($objectList as $primaryId => $refArr) {
			foreach ($refArr as $ref) {
				foreach ($data as $item) {
					if ($item['model'] == $ref['model'] && $item['foreign_key'] == $ref['id']) {
						$final[$primaryId][] = $item['user_id'];
					}
				}
			}

			if (isset($final[$primaryId])) {
				$final[$primaryId] = array_unique($final[$primaryId]);
			}
		}

		return $final;
	}
}
