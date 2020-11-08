<?php
App::uses('AppModel', 'Model');

class CustomRolesAppModel extends AppModel {
	public $tablePrefix = 'custom_roles_';

	// handler for custom roles permissions
	protected function _setPermission($action, $requestorId, $model, $foreignKey)
	{
		$mainColumn = 'user_id';
		if ($this->alias == 'CustomRolesGroup') {
			$mainColumn = 'group_id';
		}

		$requestorData = $this->find('first', [
			'conditions' => [
				$this->alias . '.' . $mainColumn => $requestorId
			],
			'fields' => [
				$this->alias . '.id'
			],
			'recursive' => -1
		]);

		$customId = $requestorData[$this->alias]['id'];
		$requestor = [
			$this->modelFullName() => [
				'id' => $customId
			]
		];

		// propagate specific object's acl permissions to different object
		if ($model == 'DataAssetSetting') {
			$DataAssetSetting = ClassRegistry::init('DataAssetSetting');
			$DataAssetSetting->id = $foreignKey;

			$foreignKey = $DataAssetSetting->field('data_asset_instance_id');
			$model = 'DataAssetInstance';
		}
		
		$object = [
			$model => [
				'id' => $foreignKey
			]
		];

		$this->initAcl();
		return $this->Acl->{$action}($requestor, $object, '*');
	}

	/**
	 * Initialize ACL but with Visualisation ACL adapter.
	 */
	public function initAcl($controller = null) {
		if ($this->Acl instanceof AclComponent) {
			return $this->Acl;
		}
		
		$originalAdapter = Configure::read('Acl.classname');
		Configure::write('Acl.classname', Configure::read('Visualisation.Acl.classname'));

		if (!$controller) {
			$controller = new Controller(new CakeRequest());
		}
		$collection = new ComponentCollection();
		$this->Acl = new AclComponent($collection);
		$this->Acl->startup($controller);
		$this->Aco = $this->Acl->Aco;
		$this->Aro = $this->Acl->Aro;
		$this->controller = $controller;

		Configure::write('Acl.classname', $originalAdapter);

		return $this->Acl;
	}
}
