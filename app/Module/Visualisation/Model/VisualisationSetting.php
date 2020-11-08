<?php
/**
 * @package       Visualisation.Model
 */
App::uses('VisualisationAppModel', 'Visualisation.Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('UserFields', 'UserFields.Lib');
App::uses('VisualisationLogBehavior', 'Visualisation.Model/Behavior');

class VisualisationSetting extends VisualisationAppModel {
	public $cacheSources = false;
	public $useTable = 'settings';

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'status'
			)
		),
		'ModuleSettings.ModuleSettings',
		'UserFields.UserFields' => [
			'fields' => [
				'ExemptedUser' => [
					'mandatory' => false,
					'customRolesInit' => false
				]
			]
		],
		'SystemLogs.SystemLogs',
		'Visualisation.VisualisationLog',
	);

	public $hasAndBelongsToMany = array(
		// worflow owners
		/*'ExemptedUser' => array(
			'className' => 'User',
			'with' => 'Visualisation.VisualisationSettingsUser',
			'joinTable' => 'visualisation_settings_users',
			'foreignKey' => 'visualisation_setting_id',
			'associationForeignKey' => 'user_id'
		)*/
	);

	public $validate = array(
		'status' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Status is required'
			),
			'callable' => [
				'rule' => ['callbackValidation', ['VisualisationSetting', 'statuses']],
				'message' => 'Status is not correct'
			]
		),
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Visualisation Settings');

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
			'status' => array(
				'label' => __('Enabled'),
				'editable' => true,
				'type' => 'toggle',
				'description' => __('Check to enable Visualisation for this section')
			),
			'ExemptedUser' => $UserFields->getFieldDataEntityData($this, 'ExemptedUser', [
				'label' => __('Exempted Users'), 
				'description' => __('User accounts you select here (from System / Settings / User Management) will be excempted from visualisations conditions, meaning they will be able to see all objects.<br><br>REMEMBER: you dont need to add users that are part of the "Admin" group as they are by default excempted.')
			])
		);

		parent::__construct($id, $table, $ds);
	}

	public function getSystemLogsConfig() {
		return VisualisationLogBehavior::getVisualisationLogsConfig();
	}

	public function beforeSave($options = array()){
		$ret = true;

		// temporarily pull data out directly from data
		// todo pull via - @see UserFieldsBehavior
		$users = Hash::extract($this->data['ExemptedUser']['ExemptedUser'], '{n}.user_id');
		$groups = Hash::extract($this->data['ExemptedUserGroup']['ExemptedUserGroup'], '{n}.group_id');

		$ret &= $this->_removeExemptedPermissions([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationSettingsUser'),
			'selectedEntries' => $users
		]);

		$ret &= $this->_removeExemptedPermissions([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationSettingsGroup'),
			'selectedEntries' => $groups
		]);

		return true;
	}

	// removal of allow permission from a set of objects 
	protected function _removeExemptedPermissions($options = []) {
		extract($options);
		$ret = true;

		$model = $this->data[$this->alias]['model'];

		$existsExtracted = $this->_findExistingExtracted($model, null, $UserObject);
		$removedEntries = array_diff($existsExtracted, $selectedEntries);
		foreach ($removedEntries as $entryId) {
			$ret &= $UserObject->unshare($entryId, [$model, ''], false);
		}
	}

	public function afterSave($created, $options = array())
	{
		$ret = true;

		// temporarily pull data out directly from data
		// todo pull via - @see UserFieldsBehavior
		$users = Hash::extract($this->data['ExemptedUser']['ExemptedUser'], '{n}.user_id');
		$groups = Hash::extract($this->data['ExemptedUserGroup']['ExemptedUserGroup'], '{n}.group_id');

		$ret &= $this->_saveExemptedPermission([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationSettingsUser'),
			'selectedEntries' => $users
		]);

		$ret &= $this->_saveExemptedPermission([
			'UserObject' => ClassRegistry::init('Visualisation.VisualisationSettingsGroup'),
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

		$existsExtracted = $this->_findExistingExtracted($model, null, $UserObject);
		$newEntries = array_diff($selectedEntries, $existsExtracted);
		foreach ($newEntries as $entryId) {
			$ret &= $UserObject->share($entryId, [$model, ''], true);
		}

		return $ret;
	}

	/**
	 * Is a visualisation enabled on a specified section.
	 */
	public function isEnabled($model) {
		return (bool) $this->find('count', [
			'conditions' => [
				$this->alias . '.model' => $model,
				$this->alias . '.status' => self::STATUS_ENABLED
			],
			'recursive' => -1
		]);
	}

	public function getItem($_model) {
		$data = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.model' => $_model
			),
			'recursive' => 1
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		return $data;
	}

	public function syncObject() {
		return true;
	}

	/**
	 * Check if a setting row exists in database.
	 * 
	 * @param  string  $model Model.
	 * @return bool           True if exists, false otherwise.
	 */
	public function itemExists($model) {
		return (bool)$this->find('count', array(
			'conditions' => array(
				$this->alias . '.model' => $model
			),
			'recursive' => -1
		));
	}

	/**
	 * Get the model aliases kept in the database, to manage visualisation sections.
	 * 
	 * @return array Model names.
	 */
	public function getModelAliases() {
		$conditions = [];

		if (!AppModule::loaded('VendorAssessments')) {
			$conditions[] = $this->alias . ".model NOT LIKE 'VendorAssessment%'";
		}

		if (!AppModule::loaded('AccountReviews')) {
			$conditions[] = $this->alias . ".model NOT LIKE 'AccountReview%'";
		}

		if (!empty($conditions)) {
			$conditions = implode(' AND ', $conditions);
		}

		$models = $this->find('list', [
			'conditions' => [$conditions],
			'fields' => [
				$this->alias . '.' . $this->primaryKey,
				$this->alias . '.model'
			],
			'recursive' => -1
		]);

		return $models;
	}

	/*
	 * Workflow Setting statuses.
	 * @access static
	 */
	 public static function statuses($value = null) {
		$options = array(
			self::STATUS_DISABLED => __('Disabled'),
			self::STATUS_ENABLED => __('Enabled')
		);
		return parent::enum($value, $options);
	}
	const STATUS_DISABLED = 0;
	const STATUS_ENABLED = 1;

}
