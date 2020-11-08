<?php
App::uses('CustomRolesAppModel', 'CustomRoles.Model');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('AclComponent', 'Controller/Component');
App::uses('DbAcl', 'Model');
App::uses('Shell', 'Console');

class CustomRolesGroup extends CustomRolesAppModel {
	public $useTable = 'groups';
	public $cacheSources = false;

	public $belongsTo = [
		'Group' => [
			'className' => 'Visualisation.VisualisationGroup',
			'foreignKey' => 'group_id'
		]
	];

	public $actsAs = array(
		'Acl' => array('type' => 'requester')
	);

	public function parentNode($type) {
		if (isset($this->data['CustomRolesGroup']['group_id'])) {
			$group_id = $this->data['CustomRolesGroup']['group_id'];
		}
		else {
			$group_id = $this->field('group_id');
		}

		if ($group_id) {
			$node = [
				'Visualisation.VisualisationGroup' => [
					'id' => $group_id
				]
			];

			return $node;
		}

		trigger_error('CustomRolesGroup::parentNode() method must return some parent value and not be null!');
		return null;
	}

	/**
	 * Synchronize a single group into custom role table that holds alternative group instance.
	 */
	public function syncSingleObject($group_id) {
		$ret = true;

		$data = [
			'group_id' => $group_id
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

	public function setPermission($action, $group_id, $model, $foreignKey) {
		return $this->_setPermission($action, $group_id, $model, $foreignKey);
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
