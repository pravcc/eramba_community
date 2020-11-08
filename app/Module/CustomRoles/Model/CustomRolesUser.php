<?php
App::uses('CustomRolesAppModel', 'CustomRoles.Model');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('AclComponent', 'Controller/Component');
App::uses('DbAcl', 'Model');
App::uses('Shell', 'Console');

class CustomRolesUser extends CustomRolesAppModel {
	public $useTable = 'users';
	public $cacheSources = false;

	public $belongsTo = [
		'User' => [
			'className' => 'Visualisation.VisualisationUser',
			'foreignKey' => 'user_id'
		]
	];

	public $actsAs = array(
		'Acl' => array('type' => 'requester')
	);

	public function parentNode($type) {
		if (isset($this->data['CustomRolesUser']['user_id'])) {
			$userId = $this->data['CustomRolesUser']['user_id'];
		}
		else {
			$userId = $this->field('user_id');
		}

		if ($userId) {
			$node = [
				'Visualisation.VisualisationUser' => [
					'id' => $userId
				]
			];

			return $node;
		}

		trigger_error('CustomRolesUser::parentNode() method must return some parent value and not be null!');
		return null;
	}

	/**
	 * Synchronize a single user into custom role table that holds alternative user instance.
	 */
	public function syncSingleObject($userId) {
		$ret = true;

		$data = [
			'user_id' => $userId
		];
		
		$count = $this->find('count', [
			'conditions' => $data,
			'recursive' => -1
		]);

		if (!$count) {
			$this->create();
			$this->set($data);
			$ret &= $this->save();
		}

		return $ret;
	}

	public function setPermission($action, $userId, $model, $foreignKey) {
		return $this->_setPermission($action, $userId, $model, $foreignKey);
	}

	public function afterSave($created, $options = array()) {
		// clear generic cache that holds all table data used in ACL
		Cache::delete('custom_roles_data', 'custom_roles');
	}

	public function afterDelete($cascade = true) {
		// clear generic cache that holds all table data used in ACL
		Cache::delete('custom_roles_data', 'custom_roles');
	}
}
