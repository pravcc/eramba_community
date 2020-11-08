<?php
/**
 * Visualisation Data Migration Library Class.
 */

App::uses('CakeObject', 'Core');
App::uses('ClassRegistry', 'Utility');

/**
 * @package		Visualisation.Lib
 */
class AppVisualisationMigration extends CakeObject {

	/**
	 * Variable that holds data between table modifications to properly restore them into the updated schema.
	 * 
	 * @var array
	 */
	protected $_data;

	public function __construct() {
		$this->_data = null;

	}

	public function loadModels() {
		ClassRegistry::flush();

		$this->VisualisationSettingsUser = ClassRegistry::init('Visualisation.VisualisationSettingsUser');
		$this->VisualisationShare = ClassRegistry::init('Visualisation.VisualisationShare');
		$this->UserFieldsUser = ClassRegistry::init('UserFields.UserFieldsUser');

		$this->VisualisationSettingsUser->cacheSources = false;
		$this->VisualisationShare->cacheSources = false;
		$this->UserFieldsUser->cacheSources = false;
	}

	/**
	 * Migrate Visualisation share data from PRE multiple-groups functionality to multiple groups.
	 * For update process from release 48 to release 49.
	 * 
	 * @return bool True on success, False otherwise
	 */
	public function beforeSchemaUpdate($direction = 'up')
	{
		$ret = true;
		$this->loadModels();

		$ret &= ClassRegistry::init('Setting')->deleteCache(null);
		if ($direction === 'up') {
			// 
			// visualisation exempted users
			// 
			$oldData = $this->VisualisationSettingsUser->find('all', [
				'recursive' => -1
			]);

			foreach ($oldData as $item) {
				$conds = [
					'UserFieldsUser.model' => 'VisualisationSettingsUser',
					'UserFieldsUser.field' => 'ExemptedUser',
					'UserFieldsUser.user_id' => $item['VisualisationSettingsUser']['user_id']
				];

				$userField = $this->UserFieldsUser->find('first', [
					'conditions' => $conds,
					'fields' => [
						'UserFieldsUser.id'
					],
					'recursive' => -1
				]);

				if (empty($userField)) {
					throw new CakeException('Visualisation Migration Error for exampted users - User Field not found: ' . print_r($conds, true), 1);
				}

				unset($item['VisualisationSettingsUser']['user_id']);
				$item['VisualisationSettingsUser']['user_fields_user_id'] = $userField['UserFieldsUser']['id'];
				$this->_data['VisualisationSettingsUser'][] = $item;
			}

			// 
			// visualisation share users
			// 
			$oldData = $this->VisualisationShare->find('all', [
				'joins' => [
					0 =>[
						'table' => 'aros_acos',
						'alias' => 'Permission',
						'type' => 'INNER',
						'conditions' => [
							$this->VisualisationShare->alias . '.aros_acos_id = Permission.id'
						]
					],
					1 => [
						'table' => 'acos',
						'alias' => 'Aco',
						'type' => 'INNER',
						'conditions' => [
							'Permission.aco_id = Aco.id'
						]
					]
				],
				'fields' => [
					'VisualisationShare.id',
					'VisualisationShare.aros_acos_id',
					'VisualisationShare.user_id',
					'VisualisationShare.created',
					'Aco.model',
					'Aco.foreign_key'
				],
				'recursive' => -1,
				'callbacks' => false
			]);

			foreach ($oldData as $item) {
				$conds = [
					'UserFieldsUser.model' => 'VisualisationShareUser',
					'UserFieldsUser.field' => 'SharedUser',
					'UserFieldsUser.user_id' => $item['VisualisationShare']['user_id']
				];

				$userField = $this->UserFieldsUser->find('first', [
					'conditions' => $conds,
					'fields' => [
						'UserFieldsUser.id'
					],
					'recursive' => -1
				]);

				if (empty($userField)) {
					throw new CakeException('Visualisation Migration Error for shared users - User Field not found: ' . print_r($conds, true), 1);
				}

				$model = $item['Aco']['model'];
				$foreignKey = $item['Aco']['foreign_key'];

				$newItem['VisualisationShareUser'] = $item['VisualisationShare'];
				unset($newItem['VisualisationShareUser']['user_id']);

				$newItem['VisualisationShareUser']['user_fields_user_id'] = $userField['UserFieldsUser']['id'];
				$newItem['VisualisationShareUser']['model'] = $model;
				$newItem['VisualisationShareUser']['foreign_key'] = $foreignKey;

				$this->_data['VisualisationShareUser'][] = $newItem;
			}
		}

		if ($direction == 'down') {
			// 
			// visualisation exempted users
			// 
			$oldData = $this->VisualisationSettingsUser->find('all', [
				'recursive' => -1
			]);

			foreach ($oldData as $item) {
				$conds = [
					'UserFieldsUser.id' => $item['VisualisationSettingsUser']['user_fields_user_id']
				];

				$userField = $this->UserFieldsUser->find('first', [
					'conditions' => $conds,
					'fields' => [
						'UserFieldsUser.user_id'
					],
					'recursive' => -1
				]);

				if (empty($userField)) {
					throw new CakeException('Visualisation Migration Error for exampted users - User Field not found: ' . print_r($conds, true), 1);
				}

				unset($item['VisualisationSettingsUser']['user_fields_user_id']);
				$item['VisualisationSettingsUser']['user_id'] = $userField['UserFieldsUser']['user_id'];
				$this->_data['VisualisationSettingsUser'][] = $item;
			}

			// 
			// visualisation share users
			// 
			$this->VisualisationShareUser = ClassRegistry::init('Visualisation.VisualisationShareUser');
			$this->VisualisationShareUser->cacheSources = false;

			$oldData = $this->VisualisationShareUser->find('all', [
				'fields' => [
					'aros_acos_id',
					'user_fields_user_id'
				],
				'recursive' => -1
			]);

			foreach ($oldData as $item) {
				$conds = [
					'UserFieldsUser.id' => $item['VisualisationShareUser']['user_fields_user_id']
				];

				$userField = $this->UserFieldsUser->find('first', [
					'conditions' => $conds,
					'fields' => [
						'UserFieldsUser.user_id'
					],
					'recursive' => -1
				]);

				if (empty($userField)) {
					throw new CakeException('Visualisation Migration Error for shared users - User Field not found: ' . print_r($conds, true), 1);
				}

				$newItem['VisualisationShare'] = $item['VisualisationShareUser'];
				unset($newItem['VisualisationShare']['user_fields_user_id']);

				$newItem['VisualisationShare']['user_id'] = $userField['UserFieldsUser']['user_id'];

				$this->_data['VisualisationShare'][] = $newItem;
			}
		}
		
		return $ret;
	}

	public function afterSchemaUpdate($direction = 'up') {
		$ret = true;

		$this->loadModels();
		$ret &= ClassRegistry::init('Setting')->deleteCache(null);
		if ($direction === 'up') {
			if (isset($this->_data['VisualisationSettingsUser'])) {
				$ret &= $this->VisualisationSettingsUser->deleteAll('1=1', false);

				foreach ($this->_data['VisualisationSettingsUser'] as $item) {
					$this->VisualisationSettingsUser->create();
					$this->VisualisationSettingsUser->set($item);
					$ret &= $this->VisualisationSettingsUser->save();
				}
			}

			if (isset($this->_data['VisualisationShareUser'])) {
				$ret &= $this->VisualisationShare->deleteAll('1=1', false);

				$this->VisualisationShareUser = ClassRegistry::init('Visualisation.VisualisationShareUser');
				$this->VisualisationShareUser->cacheSources = false;

				foreach ($this->_data['VisualisationShareUser'] as $item) {
					$shareItem = $this->VisualisationShare->find('first', [
						'conditions' => [
							'model' => $item['VisualisationShareUser']['model'],
							'foreign_key' => $item['VisualisationShareUser']['foreign_key']
						],
						'fields' => ['id'],
						'recursive' => -1
					]);

					if (!empty($shareItem)) {
						$item['VisualisationShareUser']['visualisation_share_id'] = $shareItem['VisualisationShare']['id'];
					}
					else {
						$this->VisualisationShare->create();
						$this->VisualisationShare->set([
							'model' => $item['VisualisationShareUser']['model'],
							'foreign_key' => $item['VisualisationShareUser']['foreign_key']
						]);
						$ret &= $this->VisualisationShare->save(null, [
							'callbacks' => false,
							'fieldList' => ['model', 'foreign_key']
						]);

						$item['VisualisationShareUser']['visualisation_share_id'] = $this->VisualisationShare->id;
					}

					unset($item['VisualisationShareUser']['model']);
					unset($item['VisualisationShareUser']['foreign_key']);

					$this->VisualisationShareUser->create();
					$this->VisualisationShareUser->set($item);
					$ret &= $this->VisualisationShareUser->save();
				}
			}
			
		}

		if ($direction === 'down') {
			if (isset($this->_data['VisualisationSettingsUser'])) {
				$ret &= $this->VisualisationSettingsUser->deleteAll('1=1', false);

				foreach ($this->_data['VisualisationSettingsUser'] as $item) {
					$this->VisualisationSettingsUser->create();
					$this->VisualisationSettingsUser->set($item);
					$ret &= $this->VisualisationSettingsUser->save();
				}
			}

			if (isset($this->_data['VisualisationShare'])) {
				$ret &= $this->VisualisationShare->deleteAll('1=1', false);

				$this->VisualisationShare = ClassRegistry::init('Visualisation.VisualisationShare');
				$this->VisualisationShare->cacheSources = false;

				foreach ($this->_data['VisualisationShare'] as $item) {
					$this->VisualisationShare->create();
					$this->VisualisationShare->set($item);
					$ret &= $this->VisualisationShare->save();
				}
			}
			
		}

		return $ret;
	}


}