<?php
/**
 * Visualisation Library Class.
 */

App::uses('AclObjectExtras', 'Visualisation.Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('CakeLog', 'Log');
App::uses('Hash', 'Utility');

/**
 * Shell for Visualisation and its nodes sync.
 *
 * @package		Visualisation.Lib
 */
class Visualisation extends AclObjectExtras {

/**
 * Root node name.
 *
 * @var string
 **/
	public $rootNode = 'visualisation';

/**
 * Start up this class.
 *
 * @return void
 **/
	public function startup($controller = null) {
		parent::startup($controller);

		$this->Aro = $this->Acl->Aro;
	}

	public function permissions() {
		$G = ClassRegistry::init('Group');
		$groups = $G->find('list');

		$ret = true;
		foreach ($groups as $id => $name) {
			$G->id = $id;

			// trigger callback that sets up permission defaults
			$ret &= $G->afterSave(true, []);
		}
		
		return $ret;
	}

	public function sync_objects($params = array()) {
		$ret = true;
		$ret &= $root = $this->_checkNode('visualisation', 'visualisation', null, 'Aco');
		$rootId = $root['Aco']['id'];

		$ret &= $models = $this->_checkNode('models', 'models', $rootId, 'Aco');
		$modelsId = $models['Aco']['id'];

		$ret &= $objects = $this->_checkNode('objects', 'objects', $rootId, 'Aco');
		$objectsId = $objects['Aco']['id'];

		$models = $this->getModelsToSync();
		array_unshift($models, 'CompliancePackageRegulator');
		
		foreach ($models as $modelName) {
			$Model = ClassRegistry::init($modelName);
			if (!$Model->Behaviors->enabled('Visualisation')) {
				continue;
			}

			if ($Model->Behaviors->enabled('SoftDelete')) {
				$configSoftDelete = $Model->softDelete(null);
				$Model->softDelete(false);
			}

			// models
			// $parentId = $modelsId;
			$node = $Model->parentSectionNode();
			// $node = $this->parseNodeToCheck($Model->parentSectionNode()); //bug in sync
			$parentNode = $this->Aco->node($node);

			$parentId = null;
			if ($parentNode !== null) {
				$parentId = $parentNode[0][$this->Aco->alias][$this->Aco->primaryKey];
			}

			$ret &= $sectionNode = $this->_checkNode([$modelName, null], "$modelName::", $parentId, 'Aco');

			// objects
			
			$query = [
				'fields' => [$Model->primaryKey],
				'recursive' => -1
			];
			$data = $Model->find('list', $query);
			// foreach each one of the row and sync it
			foreach ($data as $itemId) {
				$Model->id = $itemId;

				$node = $Model->parentNode('Aco');

				// in case parent record is non-existent or DB structure became corrupt, which occurs only in
				// foreign key-less tables having relation using association, enforcing object inheritance in visualisation
				// for example: Risk Review -> Risk
				if ($Model instanceof InheritanceInterface) {
					$parentModel = $Model->parentModel();
					$parentCheckId = $node[$parentModel]['id'];

					$parentExists = ClassRegistry::init($parentModel)->find('count', [
						'conditions' => [
							'id' => $parentCheckId
						],
						'recursive' => -1,
						'softDelete' => false
					]);

					if (!$parentExists) {
						$arg1 = $Model->alias . '.' . $Model->id;
						$arg2 = $parentModel . '.' . $parentCheckId;

						$log = __('Inherited (child) object still belongs to main object without DB record (was removed completely). Invalid object %s requested a parent node that does not exist %s.');

						CakeLog::write('error', sprintf($log, $arg1, $arg2));
						continue;
					}
				}

				// $node = $this->parseNodeToCheck($Model->parentNode()); //bug in sync
				$parentNode = $this->Aco->node($node);
				$parentId = null;
				if ($parentNode !== null) {
					$parentId = $parentNode[0][$this->Aco->alias][$this->Aco->primaryKey];
				}


				$ret &= $this->_checkNode([$modelName, $itemId], "$modelName::$itemId", $parentId, 'Aco');
			}

			if ($Model->Behaviors->enabled('SoftDelete')) {
				$Model->softDelete($configSoftDelete);
			}
		}

		if ($ret) {
			$this->out(__('<success>Section objects ACL Sync successful.</success>'));
		}
		else {
			$this->out(__('<error>Section objects ACL Sync failed!</error>'));
		}

		return $ret;
	}

	public function sync_users() {
		$ret = true;
		
		ClassRegistry::init("Setting")->deleteCache(null);
		ClassRegistry::flush();
		ClassRegistry::init("Setting")->deleteCache(null);
		
		$User = ClassRegistry::init('Visualisation.VisualisationUser');
		$User->cacheSources = false;
		$users = $this->getUsersToSync($User);

		// lets foreach all users and check their ARO node
		foreach ($users as $userId) {
			$User->id = $userId;

			// get the group of the specified user first
			$group = $User->parentNode('Aro');

			// fallback for update process when parent node returns obsolete results
			if (!isset($group['Group']['id'])) {
				$groups = $User->find('first', array(
					'conditions' => array(
						$User->alias . '.id' => $User->id
					),
					'contain' => array(
						'Group' => array(
							'fields' => array(
								'Group.id'
							)
						)
					)
				));

				$groups = Hash::extract($groups, 'Group.{n}.id');
				$group = array('Group' => array('id' => $groups));
			}

			foreach ($group['Group']['id'] as $groupId) {
				// create a group node in case its missing and get it
				$ret &= $groupNode = $this->_checkNode(['Group', $groupId], null, null,  'Aro');

				if ($groupNode === false) {
					continue;
				}
				
				// get the group node ID in the Aros table
				$groupNodeId = $groupNode['Aro']['id'];

				// create a user node in case its missing
				$ret &= $this->_checkNode(['User', $userId], null, $groupNodeId, 'Aro');
			}
		}

		if ($ret) {
			$this->out(__('<success>Users ACL Sync successful.</success>'));
		}
		else {
			$this->out(__('<error>Users ACL Sync failed!</error>'));
		}

		return $ret;
	}

	public function getUsersToSync(User $User) {
		$users = $User->find('list', [
			'fields' => [
				$User->alias . '.' . $User->primaryKey
			],
			'recursive' => -1
		]);

		return $users;
	}



}
