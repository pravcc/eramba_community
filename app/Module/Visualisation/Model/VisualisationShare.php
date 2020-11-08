<?php
App::uses('VisualisationAppModel', 'Visualisation.Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('UserFields', 'UserFields.Lib');
App::uses('VisualisationLogBehavior', 'Visualisation.Model/Behavior');

class VisualisationShare extends VisualisationAppModel {
	public $useTable = 'share';

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'model', 'foreign_key'
			)
		),
		'ModuleSettings.ModuleSettings',
		'UserFields.UserFields' => [
			'fields' => [
				'SharedUser' => [
					'mandatory' => false,
					'customRolesInit' => false
				]
			]
		],
		'SystemLogs.SystemLogs',
		'Visualisation.VisualisationLog',
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Visualisation - Share');

		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			)
		);

		$this->fieldData = array(
			'model' => array(
				'label' => __('Section'),
				'editable' => false
			),
			'foreign_key' => array(
				'label' => __('Object'),
				'editable' => false
			),
			'SharedUser' => $UserFields->getFieldDataEntityData($this, 'SharedUser', [
				'label' => __('Share'), 
				'description' => __('Share an object to other users.')
			])
		);

		parent::__construct($id, $table, $ds);
	}

	public function getSystemLogsConfig() {
		return VisualisationLogBehavior::getVisualisationLogsConfig();
	}

	/**
	 * Find all shared objects within a section and get a list of IDs.
	 * 
	 * @param  string|array $model Model or array of model aliases to get a structured array list.
	 * @return array       	       Shared list.
	 */
	public function listAll($model) {
		if (is_array($model)) {
			$ret = [];

			foreach ($model as $alias) {
				$ret = am($ret, $this->listAll($alias));
			}

			return $ret;
		}

		$data = $this->find('list', [
			'conditions' => [
				'Aco.model' => $model,
				'Permission._read' => 1
			],
			'fields' => [
				'Aco.foreign_key'
			],
			// 'group' => ['Aco.foreign_key'],
			'joins' => $this->getJoins(),
			'recursive' => -1
		]);

		return [$model => $data];
	}

	public function getItem($model, $foreignKey) {
		$sync = $this->syncObject($model, $foreignKey);
		if (!$sync) {
			return false;
		}

		$data = $this->find('first', array(
			'conditions' => [
				$this->alias . '.model' => $model,
				$this->alias . '.foreign_key' => $foreignKey
			],
			'recursive' => 2
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		return $data;
	}

	/**
	 * Sync an object to work with visualisation share functionality.
	 * 
	 * @param  Model  $model      Model name
	 * @param  int    $foreignKey Foreign key of the object
	 * @return bool               True on success, False on failure
	 */
	public function syncObject($model, $foreignKey) {
		$data = [
			'model' => $model,
			'foreign_key' => $foreignKey
		];

		$count = $this->find('count', array(
			'conditions' => $data,
			'recursive' => -1
		));

		if (!$count) {
			$this->create();
			return (bool) $this->save($data);
		}

		return true;
	}

	public function beforeSave($options = array()){
		$ret = true;

		if (!isset($this->data['SharedUser']['SharedUser'])) {
			return true;
		}

		// temporarily pull data out directly from data
		// todo pull via - @see UserFieldsBehavior
		$users = Hash::extract($this->data['SharedUser']['SharedUser'], '{n}.user_id');
		$groups = Hash::extract($this->data['SharedUserGroup']['SharedUserGroup'], '{n}.group_id');

		$ret &= $this->_removeExemptedPermissions([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationShareUser'),
			'selectedEntries' => $users
		]);

		$ret &= $this->_removeExemptedPermissions([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationShareGroup'),
			'selectedEntries' => $groups
		]);
	}

	// removal of allow permission from a set of objects 
	protected function _removeExemptedPermissions($options = []) {
		extract($options);
		$ret = true;

		$model = $this->data[$this->alias]['model'];
		$foreignKey = $this->data[$this->alias]['foreign_key'];

		$existsExtracted = $this->_findExistingExtracted($model, $foreignKey, $UserObject);
		$removedEntries = array_diff($existsExtracted, $selectedEntries);
		foreach ($removedEntries as $entryId) {
			$ret &= $UserObject->unshare($entryId, [$model, $foreignKey], false);
		}
	}
	
	public function afterSave($created, $options = array())
	{
		$ret = true;

		if (!isset($this->data['SharedUser']['SharedUser'])) {
			return true;
		}

		// temporarily pull data out directly from data
		// todo pull via - @see UserFieldsBehavior
		$users = Hash::extract($this->data['SharedUser']['SharedUser'], '{n}.user_id');
		$groups = Hash::extract($this->data['SharedUserGroup']['SharedUserGroup'], '{n}.group_id');

		$ret &= $this->_saveExemptedPermission([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationShareUser'),
			'selectedEntries' => $users
		]);

		$ret &= $this->_saveExemptedPermission([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationShareGroup'),
			'selectedEntries' => $groups
		]);

		return $ret;
	}

	// adding a permission to a set of objects
	protected function _saveExemptedPermission($options = [])
	{
		extract($options);
		$ret = true;

		$model = $this->data[$this->alias]['model'];
		$foreignKey = $this->data[$this->alias]['foreign_key'];

		$existsExtracted = $this->_findExistingExtracted($model, $foreignKey, $UserObject);
		$newEntries = array_diff($selectedEntries, $existsExtracted);
		foreach ($newEntries as $entryId) {
			$ret &= $UserObject->share($entryId, [$model, $foreignKey], true);
		}

		return $ret;
	}

}
