<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('UserFields', 'UserFields.Lib');
App::uses('UserFieldsUser', 'UserFields.Model');
App::uses('UserFieldsGroup', 'UserFields.Model');

/**
 * UserFields Behavior
 */
class UserFieldsBehavior extends ModelBehavior
{
	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'fields' => [],
		'options' => []
	);

	protected $_models;

	// Postfix for User field (default is '')
	protected $userFieldNamePostfix = '';

	// Postfix for Group field (default is Group)
	protected $groupFieldNamePostfix = 'Group';

	protected static $userIdPrefix = 'User-';
	protected static $groupIdPrefix = 'Group-';

	/* Save old data before separating them and before removing prefixes so they can be used for other models - for example for some propagation fields */
	protected $oldModelData = [];

	public $settings = [];

	public $UserFieldsLib;

	/**
	 * Initialize behavior
	 * Possible settings for fields: mandatory (bool) - default true, customRolesInit (bool) - default true
	 * @param  Model  $model    Model instance
	 * @param  array  $settings Settings of behavior
	 */
	public function setup(Model $model, $settings = array())
	{
		$tempSettings = [];
		if (array_key_exists('fields', $settings)) {
			foreach ($settings['fields'] as $key => $val) {
				if (is_string($key)) {
					$tempSettings['fields'][] = $key;
					$tempSettings['options'][$key] = $val;
				} else {
					$tempSettings['fields'][] = $val;
				}
			}
		}

	    if (!isset($this->settings[$model->alias])) {
	        $this->settings[$model->alias] = Hash::merge($this->_defaults, $tempSettings);
	    }

	    $this->_models[$model->alias] = $model;

	    $this->UserFieldsLib = new UserFields();

	    $this->init($model);
	}

	/**
	 * Initialize all fields, create associations of fields in given model, initialize custom roles..
	 * @param  Model  $model Model instance
	 */
	protected function init(Model $model)
	{
		$customRolesFields = array();
		foreach ($this->settings[$model->alias]['fields'] as $field) {
			$this->makeAssociations($model, $field);
			$this->addValidation($model, $field);
			
			$customRolesFields = Hash::merge($customRolesFields, $this->getAssociationsByField($model, $field));
		}

		$this->initCustomRoles($model, $customRolesFields);
		$this->initAuditableBehavior($model);
	}

	/**
	 * Get instance of model from given model alias
	 * @param  string $modelAlias Name of model which instance you want to get
	 * @return Model              Model instance
	 */
	protected function getModelInstance($modelAlias)
	{
		if (array_key_exists($modelAlias, $this->_models)) {
			return $this->_models[$modelAlias];
		} else {
			return ClassRegistry::init($modelAlias);
		}
	}

	/**
	 * Creates associations of given fields (UserFields) in given model
	 * @param  Model  $model Model instance
	 * @param  string $field UserField (name)
	 */
	protected function makeAssociations(Model $model, $field)
	{
		//
		// Users
		$userFieldName = $this->getUserFieldName($model, $field);
		$model->bindModel(
			array(
				'hasAndBelongsToMany' => array(
					$userFieldName => array(
						'with' => 'UserFields.UserFieldsUser',
						'className' => 'User',
						'joinTable' => 'user_fields_users',
						'foreignKey' => 'foreign_key',
						'associationForeignKey' => 'user_id',
						'conditions' => array(
							'UserFieldsUser.model' => $model->alias,
							'UserFieldsUser.field' => $userFieldName
						)
					)
				)
			),	
			false
		);
		//
		
		//
		// Groups
		$groupFieldName = $this->getGroupFieldName($model, $field);
		$model->bindModel(
			array(
				'hasAndBelongsToMany' => array(
					$groupFieldName => array(
						'with' => 'UserFields.UserFieldsGroup',
						'className' => 'Group',
						'joinTable' => 'user_fields_groups',
						'foreignKey' => 'foreign_key',
						'associationForeignKey' => 'group_id',
						'conditions' => array(
							'UserFieldsGroup.model' => $model->alias,
							'UserFieldsGroup.field' => $groupFieldName
						)
					)
				)
			),
			false
		);
		//
	}

	/**
	 * Get options (from settings in initialization) of given field (UserField) 
	 * @param  Model  $model Model instance
	 * @param  string $field UserField (name)
	 * @return array         Options from settings
	 */
	protected function getFieldOptions(Model $model, $field)
	{
		$options = [];
		if (array_key_exists($field, $this->settings[$model->alias]['options'])) {
			$options = $this->settings[$model->alias]['options'][$field];
		}

		return $options;
	}

	/**
	 * Find out if UserField exists in the model
	 * @param  Model  $model Model instance
	 * @param  string $field Name of UserField
	 * @return bool.         True if UserField exists otherwise false
	 */
	protected function fieldExists(Model $model, $field)
	{
		if (in_array($field, $this->settings[$model->alias]['fields'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Test if given association is one of associations created by UserFields
	 * @param  Model  $model       Model instance
	 * @param  string $association Name of HABTM association
	 * @return void
	 */
	public function belongsAssociationToUserField(Model $model, $association)
	{
		if (in_array($association, $this->getAllAssociations($model))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Custom roles initialization
	 * @param  Model  $model Model instance
	 */
	protected function initCustomRoles(Model $model)
	{
		$customRolesFields = array();
		foreach ($this->settings[$model->alias]['fields'] as $field) {
			$fieldOptions = $this->getFieldOptions($model, $field);
			if (!array_key_exists('customRolesInit', $fieldOptions) || $fieldOptions['customRolesInit'] == true) {
				//$customRolesFields = Hash::merge($customRolesFields, $this->getAssociationsByField($model, $field));
				$customRolesFields[] = $field;
			}
		}

		$model->Behaviors->load('CustomRoles.CustomRoles', array('roles' => $customRolesFields));
	}

	/**
	 * If Auditable Behavior (History) is already attached to the model, add new created HABTM associations for UserFields
	 * @param  Model  $model Model instance
	 * @return void
	 */
	protected function initAuditableBehavior(Model $model)
	{
		if ($model->Behaviors->enabled('Auditable')) {
			$model->Behaviors->load('Auditable', [
				'habtm' => $this->getAllAssociations($model)
			]);
		}
	}

	/**
	 * Add validation for given field (UserField)
	 * @param Model  $model Model instance
	 * @param string $field Name of UserField
	 */
	protected function addValidation(Model $model, $field)
	{
		$fieldOptions = $this->getFieldOptions($model, $field);
		if (!array_key_exists('mandatory', $fieldOptions) || $fieldOptions['mandatory'] == true) {
			$model->validate[$field] = array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'message' => __('You have to choose at least one user or group')
			);
		}
	}

	/**
	 * Get all associations created by this behavior for UserField
	 * @param  Model  $model Model instance
	 * @param  string $field Name of UserField
	 * @return array         Names of associations
	 */
	public function getAssociationsByField(Model $model, $field)
	{
		return array($field . $this->userFieldNamePostfix, $field . $this->groupFieldNamePostfix);
	}

	/**
	 * Get all associations of all UserFields created by this behavior
	 * @param  Model  $model Model instance
	 * @return array.        Names of associations
	 */
	public function getAllAssociations(Model $model)
	{
		$associations = [];
		foreach ($this->settings[$model->alias]['fields'] as $field) {
			$associations = Hash::merge($associations, $this->getAssociationsByField($model, $field));
		}

		return $associations;
	}

	/**
	 * Get full name of UserFieldUser (with postfix) - this full name is used when this behavior creating associations
	 * @param  Model  $model Model instance
	 * @param  string $field Name of UserField
	 * @return string        Full name of UserFieldUser
	 */
	protected function getUserFieldName(Model $model, $field)
	{
		return $field . $this->userFieldNamePostfix;
	}

	/**
	 * Get full name of UserFieldGroup (with postfix) - this full name is used when this behavior creating associations
	 * @param  Model  $model Model instance
	 * @param  string $field Name of UserField
	 * @return string        Full name of UserFieldGroup
	 */
	protected function getGroupFieldName(Model $model, $field)
	{
		return $field . $this->groupFieldNamePostfix;
	}

	/**
	 * Get prefix for id column in UserFieldUser associations
	 * @return string        Prefix user ids
	 */
	public static function getUserIdPrefix()
	{
		return self::$userIdPrefix;
	}

	/**
	 * Get prefix for id column in UserFieldGroup associations
	 * @return string        Prefix for group ids
	 */
	public static function getGroupIdPrefix()
	{
		return self::$groupIdPrefix;
	}

	/**
	 * Add related associations for main UserField association (for example related association for UserField Owner is OwnerGroup, for Author it is AuthorGroup etc.)
	 */
	public function beforeFind(Model $model, $query)
	{
		foreach ($this->settings[$model->alias]['fields'] as $field) {
			if (isset($query['contain']) && is_array($query['contain']) && in_array($field, $query['contain'])) {
				$associations = $this->getAssociationsByField($model, $field);
				foreach ($associations as $assoc) {
					if (!in_array($assoc, $query['contain'])) {
						$query['contain'][] = $assoc;
					}
				}
			}
		}

		return $query;
	}

	/**
	 * Convert data from DB after find operation - see convertDataFromDb function
	 */
	public function afterFind(Model $model, $results, $boolean = false)
	{
		foreach ($this->settings[$model->alias]['fields'] as $field) {
			$results = $this->convertDataFromDb($model, $field, $results);
		}
		return $results;
	}

	/**
	 * Find out if given users and groups ids exists, if not, validation will fail
	 */
	public function beforeValidate(Model $model, $options = array())
	{
		foreach ($this->settings[$model->alias]['fields'] as $field) {
			$data = [];
			if (!empty($model->data[$model->alias][$field])) {
				$data = $model->data[$model->alias][$field];
			} elseif (!empty($model->data[$field])) {
				if (!empty($model->data[$field][$field])) {
					$data = $model->data[$field][$field];
				} else {
					$data = $model->data[$field];
				}
			}

			$usersGroupsOptions = $this->getUsersGroupsOptions($model);
			$usersAndGroups = array_keys($usersGroupsOptions);

			$nonExistent = [];
			foreach ($data as $d) {
				if (!in_array($d, $usersAndGroups)) {
					$nonExistent[] = $d;
				}
			}

			if (!empty($nonExistent)) {
				$message = __(
					"Some items you are trying to use does not exist.\n\rThese are: <strong>%s</strong>",
					implode(', ', $nonExistent)
				);
				$model->invalidate($field, $message);
			}
		}
	}

	/**
	 * Convert data to the right format for HABTM associations before they are saved into database
	 */
	public function beforeSave(Model $model, $options = array())
	{
		$habtmAssocs = $this->getAllAssociations($model);

		//
		// Convert HABTM associations which was set directly via data array (without model name key)
		foreach ($habtmAssocs as $habtmAssoc) {
			if (isset($model->data[$habtmAssoc]) && !isset($model->data[$habtmAssoc][$habtmAssoc])) {
				$model->data[$habtmAssoc][$habtmAssoc] = $model->data[$habtmAssoc];
			}
		}
		//

		// Transform HABTM associations
		// Updates to the core resulted in below line not required anymore
		// $model->transformDataToHabtm($habtmAssocs);

		foreach ($habtmAssocs as $habtmAssoc) {
			if (!in_array($habtmAssoc, $this->settings[$model->alias]['fields']) || !isset($model->data[$habtmAssoc][$habtmAssoc])) {
				continue;
			}

			$groupFieldName = $this->getGroupFieldName($model, $habtmAssoc);

			//
			// If main UserField association is empty, create also empty array for its group association, so all HABTM will be presented in models data, otherwise groups from the UserField won't be removed
			if (empty($model->data[$habtmAssoc][$habtmAssoc]) && !isset($model->data[$groupFieldName][$groupFieldName])) {
				$model->data[$habtmAssoc][$habtmAssoc] = [];
				$model->data[$groupFieldName][$groupFieldName] = [];
				continue;
			}
			//

			$this->storeOldModelData($model, $habtmAssoc, $model->data[$habtmAssoc][$habtmAssoc]);

			//
			// Sort data into arrays by associations
			foreach ($model->data[$habtmAssoc][$habtmAssoc] as $key => $val) {
				if (!isset($model->data[$groupFieldName][$groupFieldName])) {
					$model->data[$groupFieldName][$groupFieldName] = [];
				}

				if (is_string($val) && strpos($val, self::getGroupIdPrefix()) === 0) {
					$model->data[$groupFieldName][$groupFieldName][] = $val;
					unset($model->data[$habtmAssoc][$habtmAssoc][$key]);
				}
			}
			//

			//
			// Remove prefixes from associatedForeignKeys and add fields from HABTM conditions
			$associations = $this->getAssociationsByField($model, $habtmAssoc);
			foreach ($associations as $assoc) {
				if (!isset($model->data[$assoc][$assoc]) || !isset($model->hasAndBelongsToMany[$assoc])) {
					continue;
				}

				foreach ($model->data[$assoc][$assoc] as $key => $val) {
					if (is_string($val) && (strpos($val, self::getUserIdPrefix()) === 0 || strpos($val, self::getGroupIdPrefix()) === 0)) {
						$newRecord = [];
						$newRecord[$model->hasAndBelongsToMany[$assoc]['associationForeignKey']] = (int)explode('-', $val)[1];
						foreach ($model->hasAndBelongsToMany[$assoc]['conditions'] as $condField => $condValue) {
							$condField = explode('.', $condField);
							$newRecord[end($condField)] = $condValue;
						}
						$model->data[$assoc][$assoc][$key] = $newRecord;
					}
				}
			}
			//
		}

		return true;
	}

	/**
	 * Temporary store model's data for later use
	 * @param  Model  $model Model instance
	 * @param  string $field Name of UserField
	 * @param  array $data   Model's data
	 */
	protected function storeOldModelData(Model $model, $field, $data)
	{
		$this->oldModelData[$model->alias][$field] = $data;
	}

	/**
	 * Get temporary stored model's data
	 * @param  Model  $model Model instance
	 * @param  string $field Name of UserField
	 * @return array         Stored model's data
	 */
	public function getStoredOldModelData(Model $model, $field)
	{
		return isset($this->oldModelData[$model->alias][$field]) ? $this->oldModelData[$model->alias][$field] : [];
	}

	/**
	 * Converts data from DB from separated Users and Groups tables to one UserField with data from both
	 * @param  string $field      Name of the UserField defined while model's behavior was initialized
	 * @param  array  $data       Data for converting
	 * @return array              Return converted data
	 */
	public function convertDataFromDb(Model $model, $field, $data)
	{
		$userFieldName = $this->getUserFieldName($model, $field);
		$groupFieldName = $this->getGroupFieldName($model, $field);

		foreach ($data as $r_key => $record) {
			if (isset($record[$userFieldName])) {
				foreach ($record[$userFieldName] as $u_key => $user) {
					if (!isset($user['id'])) {
						continue;
					}
					$user['id'] = self::getUserIdPrefix() . $user['id'];
					$data[$r_key][$userFieldName][$u_key] = $user;
				}
			}
		}

		foreach ($data as $r_key => $record) {
			if (isset($record[$groupFieldName])) {
				foreach ($record[$groupFieldName] as $group) {
					$group['id'] = self::getGroupIdPrefix() . $group['id'];
					$data[$r_key][$userFieldName][] = $group;
				}
			}

			unset($data[$r_key][$groupFieldName]);
		}

		return $data;
	}

	/**
	 * Save alone UserField to database
	 * @param   Model   $model    Model for which the UserField data should be saved
	 * @param   integer $recordId ForeignKey - primary key of record in model's db for which we wants to add UserField
	 * @param   string  $field    Name of the UserField defined while model's behavior was initialized
	 * @param   array   $data     Data containing ids with prefixes of users and groups in format User-1, Group-10...
	 * @return  boolean
	 */
	public function addUserFieldToDb(Model $model, $recordId, $field, $data)
	{
		if (!$this->fieldExists($model, $field)) {
			return false;
		}

		$UserFieldsUserModel = ClassRegistry::init('UserFieldsUser');
		$UserFieldsGroupModel = ClassRegistry::init('UserFieldsGroup');

		//
		// Check if data already exists and prepare new data for db
		$tempDataUsers = [];
		$tempDataGroups = [];
		$userConditions = [
			'model' => $model->alias,
			'foreign_key' => $recordId,
			'field' => $this->getUserFieldName($model, $field)
		];
		$groupConditions = [
			'model' => $model->alias,
			'foreign_key' => $recordId,
			'field' => $this->getGroupFieldName($model, $field)
		];
		$oldDataUsers = $UserFieldsUserModel->find('list', [
			'fields' => ['id', 'user_id'],
			'conditions' => $userConditions
		]);
		$oldDataGroups = $UserFieldsGroupModel->find('list', [
			'fields' => ['id', 'group_id'],
			'conditions' => $groupConditions
		]);
		$oldDataUsers = array_map('intval', $oldDataUsers);
		$oldDataGroups = array_map('intval', $oldDataGroups);

		foreach ($data as $d) {
			if (strpos($d, self::getUserIdPrefix()) === 0) {
				$user_id = (int)explode('-', $d)[1];
				if (!in_array($user_id, $oldDataUsers)) {
					$tempDataUsers[] = Hash::merge($userConditions, [
						'user_id' => $user_id
					]);
				}
			} elseif (strpos($d, $this->getGroupIdPrefix($model)) === 0) {
				$group_id = (int)explode('-', $d)[1];
				if (!in_array($group_id, $oldDataGroups)) {
					$tempDataGroups[] = Hash::merge($groupConditions, [
						'group_id' => $group_id
					]);
				}
			}
		}
		//
		
		$ret = true;
		if (!empty($tempDataUsers)) {
			$ret &= $UserFieldsUserModel->saveMany($tempDataUsers);
		}
		if (!empty($tempDataGroups)) {
			$ret &= $UserFieldsGroupModel->saveMany($tempDataGroups);
		}

		return ($ret == true) ? true : false;
	}

	/**
	 * Delete related UserFeild's data (records from UserFieldUsers and UserFieldGroups tables)
	 */
	public function deleteUserFieldsData(Model $model)
	{
		$UserFieldsUserModel = ClassRegistry::init('UserFieldsUser');
		$UserFieldsGroupModel = ClassRegistry::init('UserFieldsGroup');

		$conditions = [
			'model' => $model->alias,
			'foreign_key' => $model->id
		];
		$UserFieldsUserModel->deleteAll($conditions, false);
		$UserFieldsGroupModel->deleteAll($conditions, false);
	}

	/**
	 * Get list of all users and groups with prefixed ids and full names with types (prefixed_id => full_name_with_type)
	 * @param  Model  $model Model instance
	 * @return array         List of users and groups
	 */
	public function getUsersGroupsOptions(Model $model)
	{
		return self::getUsersGroupsOptionsList();
	}

	/**
	 * Get list of all users and groups with prefixed ids and full names with types (prefixed_id => full_name_with_type). Static access.
	 * 
	 * @param  Model  $model Model instance
	 * @return array         List of users and groups
	 */
	public static function getUsersGroupsOptionsList()
	{
		$userModel = ClassRegistry::init('User');
		$groupModel = ClassRegistry::init('Group');

		//
		// Get Users options
		$userModel->virtualFields['full_name_with_type'] = "CONCAT(`{$userModel->alias}`.`name`, ' ', `{$userModel->alias}`.`surname`, ' ', '(" . __('User') . ")')";
		$usersData = $userModel->find('list', array(
			'fields' => array('User.id', 'User.full_name_with_type'),
			'order' => array('User.full_name_with_type' => 'ASC'),
			'recursive' => -1
		));
		
		$usersOptions = array();
		foreach ($usersData as $id => $name) {
			$usersOptions[self::getUserIdPrefix() . $id] = $name;
		}
		//
		
		//
		// Get Groups options
		$groupModel->virtualFields['full_name_with_type'] = "CONCAT(`{$groupModel->alias}`.`name`, ' ', '(" . __('Group') . ")')";
		$groupsData = $groupModel->find('list', array(
			'fields' => array('Group.id', 'Group.full_name_with_type'),
			'order' => array('Group.full_name_with_type' => 'ASC'),
			'recursive' => -1
		));

		$groupsOptions = array();
		foreach ($groupsData as $id => $name) {
			$groupsOptions[self::getGroupIdPrefix() . $id] = $name;
		}
		//

		return Hash::merge($usersOptions, $groupsOptions);
	}

	/**
	 * Get users from User Field (users from UserFieldUser Assoc and from UserFieldGroup Assoc)
	 * @param  string $field        User Field (HABTM Association)
	 * @param  array  $ids          IDs of records of given User Field which should be processed
	 * @param  array  $userDbFields Fields of DB table users which will be presented in results
	 * @return array                Users loaded from DB
	 */
	public function getUserFieldUsers(Model $model, $field, $ids = [], $userDbFields = [])
	{
		if (!in_array($field, $this->settings[$model->alias]['fields'])) {
			return false;
		}

		$userFieldAssoc = $this->getUserFieldName($model, $field);
		$groupFieldAssoc = $this->getGroupFieldName($model, $field);
		
		$conditions = [];
		if (!empty($ids)) {
			$conditions = [
				$model->alias . '.' . $model->primaryKey => $ids
			];
		}

		$tempData = $model->find('all', [
			'fields' => [
				$model->alias . '.id'
			],
			'contain' => [
				$userFieldAssoc => [
					'fields' => $userDbFields
				],
				$groupFieldAssoc => [
					'User' => [
						'fields' => $userDbFields
					]
				]
			],
			'conditions' => $conditions,
			'recursive' => -1
		]);
		
		$tempUsers = [];
		foreach ($tempData as $tData) {
			$id = $tData[$model->alias]['id'];
			if (!array_key_exists($id, $tempUsers)) {
				$tempUsers[$id] = [];
			}
			foreach ($tData[$userFieldAssoc] as $tempUser) {
				if (array_key_exists('id', $tempUser)) {
					if (strpos($tempUser['id'], self::getUserIdPrefix()) === 0) {
						$userId = (int)explode('-', $tempUser['id'])[1];
						$tempUser['id'] = $userId;
						$tempUsers[$id][$userId] = $tempUser;
					} elseif (strpos($tempUser['id'], self::getGroupIdPrefix()) === 0 && array_key_exists('User', $tempUser)) {
						foreach ($tempUser['User'] as $groupUser) {
							$tempUsers[$id][$groupUser['id']] = $groupUser;
						}
					}
				}
			}
		}

		$users = [];
		foreach ($tempUsers as $key => $val) {
			$indexes = array_keys($val);
			sort($indexes);
			$users[$key] = [];
			foreach ($indexes as $index) {
				$users[$key][] = $val[$index];
			}
		}

		return $users;
	}
}