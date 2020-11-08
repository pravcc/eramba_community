<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');

/**
 * VisualisationBehavior
 */
class CustomRolesBehavior extends ModelBehavior {

	protected $_runtime = [];

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'enabled' => true,
		'roles' => []
	);

	public $settings = [];

	public $CustomRolesInstance = null;

	/**
	 * Setup
	 *
	 * @param Model $Model
	 * @param array $settings
	 * @return void
	 */
	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
		}

		$this->CustomRolesRole = ClassRegistry::init('CustomRoles.CustomRolesRole');
		$this->CustomRolesUsers = ClassRegistry::init('CustomRoles.CustomRolesUsers');
		$this->CustomRolesGroups = ClassRegistry::init('CustomRoles.CustomRolesGroups');
	}

	public function getModelSettings(Model $Model, $labels = false) {
		$list = [];

		$customRoles = $this->settings[$Model->alias]['roles'];
		foreach ($customRoles as $field) {
			$list[] = $field;
		}

		if ($labels === true) {
			$list = Hash::normalize($list);
			foreach (array_keys($list) as $field) {
				$FieldDataEntity = $Model->getFieldDataEntity($field);
				$list[$field] = $FieldDataEntity->getLabel();
			}
		}

		return $list;
	}

	protected function _getAssocation(Model $Model, $field) {
		$association = $Model->getAssociated($field);
		// temporary solution for old custom roles
		if (in_array($Model->alias, ['ComplianceAudit', 'ComplianceAuditSetting'])) {
			$FieldDataEntity = $Model->getFieldDataEntity($field);
			$field = $FieldDataEntity->getAssociationModel();
			$association['associationForeignKey'] = 'user_id';
		}

		return $association;
	}

	// get array of custom roles with human readable label
	public function getCustomRolesLabels(Model $Model, $maxNestingLvl = 2, $actualNestingLvl = 1) {
		$list = [];

		if ($actualNestingLvl > $maxNestingLvl) {
			return $list;
		}

		$customRoles = $this->settings[$Model->alias]['roles'];
		foreach ($customRoles as $field) {
			$FieldDataEntity = $Model->getFieldDataEntity($field);
			$list[$Model->alias . '.' . $field] = sprintf('%s (%s)', $FieldDataEntity->getLabel(), $Model->label());
		}

		// generally inherits certain configuration from its parent model
		if ($Model instanceof InheritanceInterface) {
			$parent = $Model->parentModel();
			$ParentModel = ClassRegistry::init($parent);
			if ($parent !== null && $ParentModel->hasMethod('getCustomRolesLabels')) {
				$list = am($list, $ParentModel->getCustomRolesLabels($maxNestingLvl, $actualNestingLvl + 1));
			}
		}

		return $list;
	}

	/**
	 * Synchronize this object with Custom Role database objects.
	 */
	public function afterSave(Model $Model, $created, $options = array()) {
		$ret = true;
		$customRoles = $this->settings[$Model->alias]['roles'];
		foreach ($customRoles as $field) {
			$ret &= $this->_processCustomRole($Model, $this->CustomRolesUsers, $field);
			
			// temporary solution for old custom roles
			if (!in_array($Model->alias, ['ComplianceAudit', 'ComplianceAuditSetting'])) {
				// we process the custom role groups at the same time this way
				$ret &= $this->_processCustomRole($Model, $this->CustomRolesGroups, $field . 'Group');
			}
		}

		return true;
	}

	/**
	 * Process a single custom role field.
	 * 
	 * @param  Model   $Model  Model.
	 * @param  string  $field  Field name.
	 * @return boolean         True on success, False on failure.
	 */
	protected function _processCustomRole(Model $Model, Model $AssociationModel, $field) {
		$ret = true;

		if (!$Model->Behaviors->enabled('UserFields')) {
			return $this->_processOldCustomRoles($Model, $field);
		}
		$association = $Model->getAssociated($field);
		list(, $with) = pluginSplit($association['with']);
		$associationIds = $Model->{$with}->find('list', [
			'conditions' => [
				$with . '.' . $association['foreignKey'] => $Model->id,
				$with . '.model' => $Model->alias,
				$with . '.field' => $field
			],
			'fields' => [
				$with . '.' . $association['associationForeignKey']
			],
			'recursive' => -1
		]);

		$associationIds = array_values($associationIds);

		$ret &= $this->deleteEntries($Model, $AssociationModel, $associationIds, $field);

		// clean out null values
		$associationIds = array_filter($associationIds);

		// process new users
		foreach ($associationIds as $associationId) {
			$ret &= $this->_processEntry($Model, $AssociationModel, $field, $associationId);
		}

		return $ret;
	}

	protected function _processOldCustomRoles(Model $Model, $field) {
		if (!$Model->hasFieldDataEntity($field)) {
			return false;
		}
			
		$ret = true;

		$FieldDataEntity = $Model->getFieldDataEntity($field);
		// case when a field is HABTM association
		if ($FieldDataEntity->isHabtm()) {
			// $association = $FieldDataEntity->getAssociationConfig();
			// compatibility hotfix for updater
			$association = $Model->getAssociated($FieldDataEntity->getFieldName());
			$with = $association['with'];
			$conditions = [
				$with . '.' . $association['foreignKey'] => $Model->id
			];
			if (!empty($association['conditions'])) {
				$conditions = array_merge($conditions, (array) $association['conditions']);
			}
			$userIds = $Model->{$with}->find('list', [
				'conditions' => $conditions,
				'fields' => [
					$with . '.' . $association['associationForeignKey']
				],
				'recursive' => -1
			]);

			$userIds = array_values($userIds);

			$ret &= $this->deleteEntries($Model, $this->CustomRolesUsers, $userIds, $field);
		}
		// single select
		else {
			$userIds = [];
			if (isset($Model->data[$Model->alias][$field])) {
				$userIds = array($Model->data[$Model->alias][$field]);
				$ret &= $this->deleteEntries($Model, $this->CustomRolesUsers, $userIds, $field);
			}
		}

		// clean out null values
		$userIds = array_filter($userIds);

		// process new users
		foreach ($userIds as $associationId) {
			$ret &= (boolean) $this->_processEntry($Model, $this->CustomRolesUsers, $field, $associationId);
		}

		return $ret;
	}

	public function deleteEntries(Model $Model, Model $AssociationModel, $associationIds, $field) {
		$association = $this->_getAssocation($Model, $field);

		// lets find rows that are obsolete
		$nonExistent = $AssociationModel->find('list', [
			'conditions' => [
				$AssociationModel->alias . '.model' => $Model->alias,
				$AssociationModel->alias . '.foreign_key' => $Model->id,
				$AssociationModel->alias . '.' . $association['associationForeignKey'] . ' !=' => $associationIds,
				$this->CustomRolesRole->alias . '.field' => $field
			],
			'fields' => [
				$AssociationModel->alias . '.' . $AssociationModel->primaryKey
			],
			'recursive' => 0
		]);

		$ret = true;

		// remove obsolete rows
		if (!empty($nonExistent)) {
			foreach ($nonExistent as $deleteId) {
				$ret &= $AssociationModel->delete($deleteId);
			}
		}

		return $ret;
	}

	/**
	 * Process a single user row that acts as a custom role.
	 * 
	 * @param  Model  $Model  Model name.
	 * @param  string $field  Field name that is used as a definition in FieldData.
	 * @param  int    $userId User ID.
	 * @return boolean        True on success, False on failure.
	 */
	protected function _processEntry(Model $Model, Model $AssociationModel, $field, $associationId) {
		$association = $this->_getAssocation($Model, $field);

		$data = array(
			'model' => $Model->name,
			'foreign_key' => $Model->id,
			$association['associationForeignKey'] => $associationId,
			$this->CustomRolesRole->alias => [
				'model' => $Model->name,
				'field' => $field
			]
		);

		// while editing an object find and set ID of the same one that should be already present
		// when it was created
		if ($customRolesAssociationId = $this->customRoleAssociatedObject($Model, $AssociationModel, $field, $associationId)) {
			$data['id'] = $customRolesAssociationId;
		}

		// in all cases, search for and define the parent custom role ID
		if ($customRoleId = $this->customRoleObject($Model, $field)) {
			$customRoleCreated = false;
			$data[$this->CustomRolesRole->alias]['id'] = $data['custom_roles_role_id'] = $customRoleId;
		}
		else {
			$customRoleCreated = true;
		}

		$AssociationModel->create();
		$ret = $AssociationModel->saveAssociated($data);
		
		if ($ret && $customRoleCreated && $this->CustomRolesInstance instanceof CustomRoles) {
			// lets put it out into the shell if there is any on runtime
			$this->CustomRolesInstance->out(__(
				'Created Aro node: <success>%s (%s)</success>',
				sprintf('%s.%s', $data[$this->CustomRolesRole->alias]['model'], $data[$this->CustomRolesRole->alias]['field']),
				sprintf('%s.%s', $this->CustomRolesRole->alias, $this->CustomRolesRole->id)
			), 1, Shell::VERBOSE);
		}

		return $ret;
	}

	// read the existing custom role user row if there is any
	public function customRoleAssociatedObject(Model $Model, Model $AssociationModel, $field, $associationId) {
		$association = $this->_getAssocation($Model, $field);

		$data = $AssociationModel->find('first', [
			'conditions' => [
				$AssociationModel->alias . '.model' => $Model->alias,
				$AssociationModel->alias . '.foreign_key' => $Model->id,
				$AssociationModel->alias . '.' . $association['associationForeignKey'] => $associationId,
				$this->CustomRolesRole->alias . '.field' => $field
			],
			'recursive' => 0
		]);

		return isset($data[$AssociationModel->alias]['id']) ? $data[$AssociationModel->alias]['id'] : null;
	}

	// read the existing custom role object if there is any to not duplicate it while saving
	public function customRoleObject(Model $Model, $field) {
		$data = $this->CustomRolesRole->find('first', [
			'conditions' => [
				'model' => $Model->alias,
				'field' => $field
			],
			'recursive' => -1
		]);

		return isset($data['CustomRolesRole']['id']) ? $data['CustomRolesRole']['id'] : null;
	}

}