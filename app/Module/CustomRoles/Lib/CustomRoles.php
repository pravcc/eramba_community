<?php
/**
 * CustomRoles Library Class.
 */

App::uses('AclObjectExtras', 'Visualisation.Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('CakeLog', 'Log');

/**
 * Shell for CustomRoles and its nodes sync.
 *
 * @package		CustomRoles.Lib
 */
class CustomRoles extends AclObjectExtras {

/**
 * Root node name.
 *
 * @var string
 **/
	public $rootNode = 'customRoles';

/**
 * Start up And load Acl Component / Aco model
 *
 * @return void
 **/
	public function startup($controller = null) {
		parent::startup($controller);
	}

	public function sync($params = array()) {
		$ret = true;

		$ret &= $this->syncCustomRoles(ClassRegistry::init('CustomRoles.CustomRolesUser'));
		$ret &= $this->syncCustomRoles(ClassRegistry::init('CustomRoles.CustomRolesGroup'));

		// $ret &= $root = $this->_checkNode($this->rootNode, $this->rootNode, null, 'Aro');
		// $rootId = $root['Aro']['id'];

		// $ret &= $allow = $this->Acl->allow($this->AclShell->parseIdentifier($this->rootNode), $this->AclShell->parseIdentifier('visualisation'));

		$models = $this->getModelsToSync();

		ClassRegistry::flush();
		// lets foreach each one of the model
		foreach ($models as $modelName) {
			$Model = ClassRegistry::init($modelName);

			// handler for disabled custom roles behavior within a model
			if (!$Model->Behaviors->enabled('CustomRoles')) {
				continue;
			}

			if ($Model->Behaviors->enabled('SoftDelete')) {
				// disable soft delete to get all items
				$configSoftDelete = $Model->softDelete(null);
				$Model->softDelete(false);
			}

			$data = $Model->find('list', [
				'fields' => [$Model->primaryKey],
				'recursive' => -1
			]);

			$CustomRolesBehavior = $Model->Behaviors->CustomRoles;
			$CustomRolesBehavior->CustomRoles = $this;

			// foreach each one of the item and trigger afterSave callback on CustomRolesBehavior
			foreach ($data as $itemId) {
				$Model->id = $itemId;

				// temporary solution for old custom roles
				if (in_array($Model->alias, ['ComplianceAudit', 'ComplianceAuditSetting'])) {
					$data = $Model->find('first', ['conditions' => [$Model->alias . '.id' => $itemId], 'recursive' => -1]);
					$Model->set($data[$Model->alias]);
				}

				$ret &= $CustomRolesBehavior->afterSave($Model, false, []);
			}

			if ($Model->Behaviors->enabled('SoftDelete')) {
				// reenable softdelete
				$Model->softDelete($configSoftDelete);
			}
		}

		if ($ret) {
			$this->out(__('<success>Custom Roles ACL Sync successful.</success>'));
		}
		else {
			$this->out(__('<error>Custom Roles ACL Sync failed!</error>'));
		}

		return $ret;
	}

	// sync objects that have been created before acl was implemented into the model
	public function syncCustomRoles(Model $CustomRoleModel) {
		$ret = true;

		App::uses('ConnectionManager', 'Model');
		$sources = ConnectionManager::getDataSource('default')->listSources();

		// new principle of custom roles sync came with later versions where this table is only added so
		// this check prevents errors occured during reset of the database for example.
		// @see 20170726164030_Release37::bumpVersion() where the sync triggers again
		if (!in_array('custom_roles_users', $sources)) {
			return true;
		}

		if ($CustomRoleModel->alias == 'CustomRolesUser') {
			$associationModel = ClassRegistry::init('User');
			$associationForeignKey = 'user_id';
		}
		else {
			$associationModel = ClassRegistry::init('Group');
			$associationForeignKey = 'group_id';
		}

		$Aro = ClassRegistry::init('Aro');
		$User = ClassRegistry::init('User');

		// check if there are some records of custom users in the table already
		$customUsersExist = $CustomRoleModel->find('list', [
			'fields' => [
				$CustomRoleModel->alias . '.id'
			],
			'recursive' => -1
		]);

		// remove non-existent Aro nodes just in case
		$ret &= $Aro->deleteAll([
			'Aro.model' => $CustomRoleModel->alias,
			'Aro.foreign_key !=' => $customUsersExist
		]);

		// find which ones are already synced and get the IDs
		$list = $Aro->find('list', [
			'conditions' => [
				'Aro.model' => $CustomRoleModel->alias
			],
			'fields' => [
				'Aro.foreign_key'
			],
			'recursive' => -1
		]);

		// then find by those IDs, already synced user IDs to sync with custom roles
		$synced = $CustomRoleModel->find('list', [
			'conditions' => [
				$CustomRoleModel->alias . '.id' => $list
			],
			'fields' => [
				$CustomRoleModel->alias . '.' . $associationForeignKey
			],
			'recursive' => -1
		]);
		
		// and find list of users without custom role sync to sync
		$data = $associationModel->find('list', [
			'conditions' => [
				$associationModel->alias . '.id !=' => $synced
			],
			'fields' => [
				$associationModel->alias . '.id'
			],
			'recursive' => -1
		]);

		try {
			foreach ($data as $id) {
				$ret &= $CustomRoleModel->syncSingleObject($id);
			}

			return $ret;
		}
		catch (CakeException $e) {
			CakeLog::write(LOG_ERR, sprintf('Custom Roles ACL synchronization was unsuccessful - %s', $e->getMessage()));
			return false;
		}
	}

}
