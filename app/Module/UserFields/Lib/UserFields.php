<?php
App::uses('UserFieldsUser', 'UserFields.Model');
App::uses('Inflector', 'Utility');
App::uses('Hash', 'Utility');

class UserFields
{
	/**
	 * Get predefined data of FieldDataEntity for UserField
	 * @param  Model  $model   Model instance
	 * @param  string $field   Name of UserField
	 * @param  array  $options Data which will overwrite predefined options
	 * @return array           Data for FieldDataEntity
	 */
	public function getFieldDataEntityData($model, $field, $options = [])
	{
		$data = [
			'label' => __('Owner'),
			'editable' => true,
			'options' => [
				'callable' => [$model, 'getUsersGroupsOptions'],
				'passParams' => false
			],
			'description' => '',
			'empty' => __('Choose users or groups'),
			'UserField' => true
		];

		$editableData = ['label', 'editable', 'description', 'empty', 'group', 'dependency', 'quickAdd', 'inlineEdit', 'renderHelper'];
		return $this->replaceEditableData($data, $options, $editableData);
	}

	/**
	 * Get predefined data of AdvancedFilters for UserField
	 * @param  string $modelAlias Model's name
	 * @param  string $field      Name of UserField
	 * @param  array  $options    Data which will overwrite predefined options
	 * @return array              Data for AdvancedFilters
	 */
	public function getAdvancedFilterFieldData($modelAlias, $field, $options = [])
	{
		$data = [
			'type' => 'multiple_select',
			// 'name' => __('Owner'),
			'show_default' => true,
			'filter' => [
				'type' => 'subquery',
				'method' => 'findComplexType',
				'userField' => $field,
				'findField' => 'UserFieldsObject' . $field . '.object_key',
				'field' => $modelAlias . '.id'
			],
			'data' => [
				'method' => 'getUsersGroupsOptions'
			],
			'many' => true,
			'fieldData' => $field
		];

		$editableData = [
			'name',
			'show_default',
			'fieldData',
			'filter' => [
				'findField',
				'field'
			]
		];
		return $this->replaceEditableData($data, $options, $editableData);
	}

	/**
	 * Get predefined data of AdvancedFilters for UserField when UserField is not in the same model as defined AdvancedFilter
	 * @param  string $modelAlias        Name of model, where AdvancedFilters are defined
	 * @param  string $foreignModelAlias Name of model, where UserField is defined
	 * @param  string $field             Name of UserField
	 * @param  array  $options           Data which will overwrite predefined options
	 * @param  string $parentModelAlias  Name of model, which will be used for connecting model from first param (modelAlias) with foreignModel (foreignModelAlias)
	 * @return array                     Data for AdvancedFilters
	 */
	public function getForeignAdvancedFilterFieldData($modelAlias, $foreignModelAlias, $field, $options = [], $parentModelAlias = '')
	{
		$data = [];
		if (empty($parentModelAlias)) {
			$data = [
				'type' => 'multiple_select',
				'name' => __('Owner'),
				'filter' => [
					'type' => 'subquery',
					'method' => 'findComplexType',
					'userField' => $field,
					'findField' => $foreignModelAlias . '.' . $field,
					'field' => $modelAlias . '.id',
				],
				'data' => [
					'method' => 'getUsersGroupsOptions'
				],
				'field' => $foreignModelAlias . '.' . $field . '.{n}.full_name_with_type',
				'many' => true,
				'containable' => [
					$foreignModelAlias => [
						$field => [
							'fields' => 'full_name_with_type'
						],
						$field . 'Group' => [
							'fields' => 'full_name_with_type'
						]
					]
				]
			];
		} else {
			$data = [
				'type' => 'multiple_select',
				'name' => __('Project Owner'),
				'filter' => [
					'type' => 'subquery',
					'method' => 'findBy' . $parentModelAlias . 'Complex',
					'userField' => $field,
					'findField' => $foreignModelAlias . '.' . $field,
					'field' => $modelAlias . '.' . Inflector::underscore($parentModelAlias) . '_id',
				],
				'data' => [
					'method' => 'getUsersGroupsOptions',
				],
				'field' => $parentModelAlias . '.' . $foreignModelAlias . '.{n}.' . $field . '.{n}.full_name_with_type',
				'many' => true,
				'containable' => [
					$parentModelAlias => [
						'fields' => ['id'],
						$foreignModelAlias => [
							$field => [
								'fields' => 'full_name_with_type'
							],
							$field . 'Group' => [
								'fields' => 'full_name_with_type'
							]
						]
					]
				]
			];
		}

		$editableData = [
			'name',
			'show_default',
			'filter' => [
				'method',
				'field'
			],
			'field',
			'fieldData'
		];

		return $this->replaceEditableData($data, $options, $editableData);
	}

	/**
	 * Get predefined data of NotificationSystem for UserField
	 * @param  string $field   Name of UserField
	 * @param  array  $options Data which will overwrite predefined options
	 * @return array           Data for NotificationSystem
	 */
	public function getNotificationSystemData($field, $options = [])
	{
		$data = [
			'field' => $field . '.{n}.full_name_with_type',
			'name' => __('Owner')
		];

		$editableData = ['name'];
		return $this->replaceEditableData($data, $options, $editableData);
	}

	/**
	 * Get predefined data of ImportArgs for UserField
	 * @param  string  $field    Name of UserField
	 * @param  array  $options   Data which will overwrite predefined options
	 * @param  boolean $optional Whether or not is this field mandatory - used for header tooltip
	 * @return array             Data for ImportArgs
	 */
	public static function getImportArgsFieldData($field, $options, $optional = false)
	{
		$headerTooltipPrefix = "";
		if (!array_key_exists('headerTooltip', $options) && $optional == true) {
			$headerTooltipPrefix = __('Optional') . '. ';
		}
		elseif (!array_key_exists('headerTooltip', $options) && $optional == false) {
			$headerTooltipPrefix = __('Mandatory') . '. ';
		}
		$headerTooltipPrefix .= __('Accepts multiple user logins or group names separated by "|". For User login use prefix "User-" and for Group name use "Group-". For example "User-admin|Group-Third Party Feedback|Group-Admin"') . '. ';
		$data = [
			'name' => __('Owner'),
			'model' => $field,
			'headerTooltip' => $headerTooltipPrefix . __('You can get the login of an user account from System / Settings / User Management or name of a group from System / Settings / Groups.'),
			'objectAutoFind' => true
		];

		$editableData = ['name', 'headerTooltip'];
		return self::replaceEditableData($data, $options, $editableData);
	}

	/**
	 * Replace predefined data with given data
	 * @param  array  $data         Predefined data
	 * @param  array  $options      Data for replacing
	 * @param  array  $editableData Data allowed for replacing
	 * @return array                New data
	 */
	private static function replaceEditableData($data, $options, $editableData)
	{
		foreach ($options as $key => $val) {
			if (in_array($key, $editableData)) {
				$data[$key] = $val;
			} elseif (array_key_exists($key, $editableData) && is_array($editableData[$key])) {
				$data[$key] = self::replaceEditableData($data[$key], $options[$key], $editableData[$key]);
			}
		}

		return $data;
	}

	/**
	 * Pass-throught method for UserFieldsBehavior's method
	 */
	public function convertDataFromDb($modelAlias, $field, $data)
	{
		$results = [];
		$model = ClassRegistry::init($modelAlias);
		if ($model->Behaviors->enabled('UserFields')) {
			$UserFieldsBehavior = $model->Behaviors->UserFields;
			$results = $UserFieldsBehavior->convertDataFromDb($model, $field, $data);
		}

		return $results;
	}

	/**
	 * Pass-throught method for UserFieldsBehavior's method
	 */
	public function addUserFieldToDb($modelAlias, $recordId, $field, $data)
	{
		$model = ClassRegistry::init($modelAlias);
		if ($model->Behaviors->enabled('UserFields')) {
			$UserFieldsBehavior = $model->Behaviors->UserFields;
			return $UserFieldsBehavior->addUserFieldToDb($model, $recordId, $field, $data);
		}

		return false;
	}

	/**
	 * 
	 * @param string $type 
	 * 
	 */
	/**
	 * Move existing fields (owner_id ...) from section table to user_fields_users table
	 * @param  string $type           up|down (Up - move data to user_fields_users table | Down - move data back from user_fields_users table to section's table)
	 * @param  string|array $model    Name of model from where will be data migrated | Array of model configuration for ClassRegistry::init() function
	 * @param  string $fields         Key => value pairs of fields Examples:
	 * Schema example:
	 * new_user_field_name => old_field_name|array(old_field_name => habtm_assoc_config)
	 * Real data example:
	 * 'Owner' => 'user_id' for db column type or 'Owner' => array('LegalOwner' => array('joinTable' => 'db_table_name', 'foreignKey' => 'legal_owner_id', 'conditions' => [])) for HABTM association type of old field
	 * @return  void
	 */
	public function moveExistingFieldsToUserFieldsTable($type = 'up', $model, $fields)
	{
		$modelClass = '';
		$modelConfig = $model;
		if (is_array($model)) {
			if (!isset($model['class'])) {
				return false;
			}

			$modelClass = $model['class'];
		} else {
			$modelClass = $model;
		}
		ClassRegistry::flush();
		$userFieldsUserModel = ClassRegistry::init('UserFieldsUser');
		$targetModel = ClassRegistry::init($modelConfig);
		
		//
		// Get ID of all users
		$userModel = ClassRegistry::init('User');
		$allUsers = $userModel->find('all', [
			'fields' => [
				'User.id'
			],
			'recursive' => -1
		]);
		$allUserIds = Hash::extract($allUsers, "{n}.User.id");
		//

		foreach ($fields as $key => $val) {
			$oldFieldType = '';
			$newField = $key;
			$oldField = '';
			$habtmConfig = [];
			if (is_array($val)) {
				$oldFieldType = 'habtm';
				$oldField = key($val);

				//
				// Set HABTM configuration
				$habtmConfig = [
					'joinTable' => Inflector::tableize($modelClass) . '_' . Inflector::underscore(Inflector::pluralize($oldField)),
					'foreignKey' => Inflector::underscore(Inflector::singularize($modelClass)) . '_id',
					'conditions' => [],
					'associationForeignKey' => 'user_id'
				];
				foreach (current($val) as $configKey => $configVal) {
					if (array_key_exists($configKey, $habtmConfig)) {
						$habtmConfig[$configKey] = $configVal;
					}
				}
				//
				
				ClassRegistry::removeObject('UserFieldsMigrationTempModel');
				$tempHabtmModel = ClassRegistry::init([
					'class' => 'UserFieldsMigrationTempModel',
					'table' => $habtmConfig['joinTable']
				]);
			} else {
				$oldFieldType = 'db_column';
				$oldField = $val;
			}

			if ($type === 'up') {
				$data = [];
				if ($oldFieldType == 'db_column') { // Migrate data from sections's table
					$initialData = $targetModel->find('all', [
						'fields' => [
							$modelClass . '.id', $modelClass . '.' . $oldField
						],
						'recursive' => -1
					]);

					foreach ($initialData as $iData) {
						if (empty($iData[$modelClass][$oldField]) || !in_array($iData[$modelClass][$oldField], $allUserIds)) {
							continue;
						}

						$data[] = [
							'model' => $modelClass,
							'foreign_key' => $iData[$modelClass]['id'],
							'field' => $newField,
							'user_id' => $iData[$modelClass][$oldField]
						];
					}
				} elseif ($oldFieldType == 'habtm') { // Migrate data from HABTM table
					$initialData = $tempHabtmModel->find('all', [
						'fields' => [
							'id', $habtmConfig['foreignKey'], $habtmConfig['associationForeignKey']
						],
						'conditions' => $habtmConfig['conditions'],
						'recursive' => -1
					]);
					foreach ($initialData as $iData) {
						if (empty($iData[$tempHabtmModel->alias][$habtmConfig['associationForeignKey']]) || !in_array($iData[$tempHabtmModel->alias][$habtmConfig['associationForeignKey']], $allUserIds)) {
							continue;
						}

						$data[] = [
							'model' => $modelClass,
							'foreign_key' => $iData[$tempHabtmModel->alias][$habtmConfig['foreignKey']],
							'field' => $newField,
							'user_id' => $iData[$tempHabtmModel->alias][$habtmConfig['associationForeignKey']]
						];
					}
				}

				if (!empty($data)) {
					$userFieldsUserModel->saveMany($data);
				}
			} elseif ($type === 'down') {
				if ($oldFieldType == 'db_column') { // Migrate data to sections's table
					$users = $userFieldsUserModel->find('all', [
						'fields' => [
							'UserFieldsUser.model', 'UserFieldsUser.foreign_key', 'UserFieldsUser.field', 'UserFieldsUser.user_id'
						],
						'conditions' => [
							'UserFieldsUser.model' => $modelClass,
							'UserFieldsUser.field' => $newField
						],
						'group' => [
							'UserFieldsUser.foreign_key'
						],
						'order' => [
							'UserFieldsUser.id'
						],
						'recursive' => -1
					]);
					foreach ($users as $user) {
						if (empty($user['UserFieldsUser']['user_id']) || !in_array($user['UserFieldsUser']['user_id'], $allUserIds)) {
							continue;
						}

						$targetModel->id = $user['UserFieldsUser']['foreign_key'];
						$data = [
							$oldField => $user['UserFieldsUser']['user_id']
						];
						$targetModel->save($data, false, [$oldField]);
					}
				} elseif ($oldFieldType == 'habtm') { // Migrate data to HABTM table
					$users = $userFieldsUserModel->find('all', [
						'fields' => [
							'UserFieldsUser.model', 'UserFieldsUser.foreign_key', 'UserFieldsUser.field', 'UserFieldsUser.user_id'
						],
						'conditions' => [
							'UserFieldsUser.model' => $modelClass,
							'UserFieldsUser.field' => $newField
						],
						'order' => [
							'UserFieldsUser.id'
						],
						'recursive' => -1
					]);

					$data = [];
					foreach ($users as $user) {
						if (empty($user['UserFieldsUser']['user_id']) || !in_array($user['UserFieldsUser']['user_id'], $allUserIds)) {
							continue;
						}
						
						$newRecord = [
							$habtmConfig['foreignKey'] => $user['UserFieldsUser']['foreign_key'],
							$habtmConfig['associationForeignKey'] => $user['UserFieldsUser']['user_id']
						];
						if (isset($habtmConfig['conditions'])) {
							foreach ($habtmConfig['conditions'] as $condField => $condValue) {
								$condField = explode('.', $condField);
								$newRecord[end($condField)] = $condValue;
							}
						}
						$data[] = $newRecord;
					}
					if (!empty($data)) {
						$tempHabtmModel->saveMany($data);
					}
				}
			}
		}
	}
}