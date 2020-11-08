<?php
App::uses('VisualisationAppModel', 'Visualisation.Model');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('AclComponent', 'Controller/Component');
App::uses('DbAcl', 'Model');
App::uses('Shell', 'Console');

abstract class VisualisationShareBase extends VisualisationAppModel {
	public $cacheSources = false;
	
	/**
	 * UserFields Behavior model name to pull data from.
	 * 
	 * @var string
	 */
	protected $_userFieldsModel = null;

	/**
	 * Field name used in UserFields functionality.
	 * 
	 * @var string
	 */
	protected $_userFieldsField = null;

	/**
	 * Column within UserFields functionality.
	 * @var null
	 */
	protected $_userFieldsColumn = null;

	/**
	 * Parent Model name which this model entries belong to.
	 * 
	 * @var string
	 */
	protected $_parentModel = null;

	/**
	 * Model name for which visualisation permission should be applied to.
	 * 
	 * @var string
	 */
	protected $_permissionModel = null;

	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'Permission' => [
			'className' => 'Permission',
			'foreignKey' => 'aros_acos_id'
		],
		// 'User'
	);

	/**
	 * Get UserFields functionality column name.
	 * 
	 * @return string
	 */
	public function getUserFieldsColumn() {
		return $this->_userFieldsColumn;
	}

	/**
	 * Get UserFields model.
	 * 
	 * @return string
	 */
	public function getUserFieldsModel() {
		return ClassRegistry::init($this->_userFieldsModel);
	}

	// Share an object to user. Grant read access in ACL and logs a sharing record about it.
	public function share($permissionForeignKey, $object, $notify = true)
	{
		$ret = true;

		$permissionId = $this->processPermission('allow', $permissionForeignKey, $object);

		// permission wasnt saved correctly
		if ($permissionId === false) {
			return false;
		}

		$parentForeignKey = $this->_findParentObject($object);
		$data = [
			'aros_acos_id' => $permissionId,
			'user_fields_' . $this->_userFieldsColumn => $this->_findUserField(
				$parentForeignKey,
				$permissionForeignKey
			),
			$this->_getParentModelColumn() => $parentForeignKey
		];

		$this->create();
		$this->set($data);
		$ret = $this->save();

		if ($ret) {
			//log shared users
			list($plugin, $name) = pluginSplit($this->_parentModel);
			$this->{$name}->visualisationLog(true, $object, $this->getAffectedUsers($this->id));

			if ($notify) {
				$ret &= (bool) $this->notify($this->id);
			}
		}

		return (bool) $ret;
	}

	/**
	 * Get users affected by item.
	 * 
	 * @param  int|array $id Id or conditions.
	 * @return array List of user Ids.
	 */
	public function getAffectedUsers($id) {
		$users = [];

		$shareData = $this->getShareData($id);

		if (!empty($shareData)) {
			$users = $this->_parseUsers($shareData['Aro']['foreign_key']);
		}

		return $users;
	}

	public function unshare($permissionForeignKey, $object, $notify = false) {
		$ret = true;

		$permissionId = $this->processPermission('inherit', $permissionForeignKey, $object);
		if ($permissionId === false) {
			return false;
		}

		$parentForeignKey = $this->_findParentObject($object);

		$data = [
			'aros_acos_id' => $permissionId,
			'user_fields_' . $this->_userFieldsColumn => $this->_findUserField(
				$parentForeignKey,
				$permissionForeignKey
			),
			$this->_getParentModelColumn() => $parentForeignKey
		];

		$users = $this->getAffectedUsers($data);

		$ret &= $this->deleteAll($data);

		if ($ret && !empty($users)) {
			//log unshared users
			list($plugin, $name) = pluginSplit($this->_parentModel);
			$this->{$name}->visualisationLog(false, $object, $users);
		}

		return (bool) $ret;
	}

	/**
	 * Get parent object of an object that belongs to the ACL.
	 */
	protected function _findParentObject($object) {
		$object = array_filter($object);
		$conds = [
			'model' => $object[0]
		];

		if (isset($object[1])) {
			$conds['foreign_key'] = $object[1];
		}

		list($plugin, $name) = pluginSplit($this->_parentModel);

		$this->_bindParentModel();
		call_user_func_array([$this->{$name}, 'syncObject'], $object);

		$data = $this->{$name}->find('first', [
			'conditions' => $conds,
			'fields' => [
				$name . '.' . $this->{$name}->primaryKey
			],
			'recursive' => -1
		]);
		
		if (empty($data)) {
			throw new NotFoundException(__('Visualisation Share record for this object was not found'));
		}

		return $data[$this->{$name}->alias][$this->{$name}->primaryKey];
	}

	/**
	 * Bind parent model.
	 * 
	 * @return void
	 */
	protected function _bindParentModel() {
		list($plugin, $name) = pluginSplit($this->_parentModel);

		$this->bindModel([
			'belongsTo' => [
				$name => [
					'className' => $this->_parentModel
				]
			]
		], false);
	}

	/**
	 * Get parent association's column name to make process with ACL more dry.
	 * 
	 * @return string Parent association's column name.
	 */
	protected function _getParentModelColumn() {
		$this->_bindParentModel();

		list($plugin, $name) = pluginSplit($this->_parentModel);
		$assoc = $this->getAssociated($name);

		return $assoc['foreignKey'];
	}

	protected function _findUserField($parentForeignKey, $permissionForeignKey) {
		list($plugin, $name) = pluginSplit($this->_userFieldsModel);
		list($parentPlugin, $parentName) = pluginSplit($this->_parentModel);

		$this->_syncUserField($parentForeignKey, $permissionForeignKey);

		$data = $this->getUserFieldsModel()->find('first', [
			'conditions' => [
				$name . '.model' => $parentName,
				$name . '.field' => $this->_userFieldsField,
				$name . '.foreign_key' => $parentForeignKey,
				$name . '.' . $this->_userFieldsColumn => $permissionForeignKey
			]
		]);

		return $data[$name]['id'];
	}

	/**
	 * Sync UserFields data.
	 */
	protected function _syncUserField($parentForeignKey, $permissionForeignKey) {
		list($plugin, $name) = pluginSplit($this->_userFieldsModel);
		list($parentPlugin, $parentName) = pluginSplit($this->_parentModel);

		if ($this->_userFieldsModel === 'UserFields.UserFieldsUser') {
			$key = 'User';
		}
		else {
			$key = 'Group';
		}

		App::uses('UserFields', 'UserFields.Lib');
		$UserFields = new UserFields();
		$ret = $UserFields->addUserFieldToDb(
			$parentName,
			$parentForeignKey,
			$this->_userFieldsField,
			[$key . '-' . $permissionForeignKey]
		);

		return $ret;
	}

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

	/**
	 * Generic method that processes a permission change request on a given objects.
	 * 
	 * @return mixed         Permission ID in case of successfully changed permission, False otherwise.
	 */
	protected function processPermission($action, $permissionForeignKey, $object) {
		$ret = true;

		$requestor = [
			$this->_permissionModel => [
				'id' => $permissionForeignKey
			]
		];

		$object = [
			$object[0] => [
				'id' => $object[1]
			]
		];

		$this->initAcl();
		$ret &= $this->Acl->{$action}($requestor, $object, '*');

		if ($ret) {
			return $this->Permission->id;
		}

		return false;
	}

	public function getShareData($id) {
		if (is_array($id)) {
			$conditions = $id;
		}
		else {
			$conditions = [
				$this->escapeField() => $id
			];
		}

		return $this->find('first', [
			'conditions' => $conditions,
			'fields' => [
				$this->alias . '.*',
				'Permission.*',
				'Aro.foreign_key'
			],
			'joins' => [
				0 =>[
					'table' => 'aros',
					'alias' => 'Aro',
					'type' => 'LEFT',
					'conditions' => [
						'Permission.aro_id = Aro.id'
					]
				],
			]
		]);
	}

	/**
	 * Sends out email about the shared object to specified users.
	 * 
	 * @param  int|array $users User or list of user IDs.
	 * @return boolean          True on success, false on failure.
	 */
	public function notify($id) {
		$ret = true;

		$shareData = $this->getShareData($id);
		if (!$shareData) {
			throw new CakeException(sprintf(
				"Notification for shared object %s within model %s doesn't exist",
				$id,
				$this->alias
			));
		}

		$object = $this->getSharedObject($shareData[$this->alias]['aros_acos_id']);
		$Model = ClassRegistry::init($object['Aco']['model']);
		$sectionLabel = $Model->label(['singular' => true]);

		// if the node is not an object but entire section
		if ($object['Aco']['foreign_key'] === null) {
			$subjectTemplate = __('A section has been shared with you (%s)');
			$template = 'Visualisation.share_section';
			$primaryTitle = $sectionLabel;
		}
		else {
			$subjectTemplate = __('An object has been shared with you (%s)');
			$template = 'Visualisation.share_section';
			$objectTitle = $Model->getRecordTitle($object['Aco']['foreign_key']);
			$primaryTitle = sprintf('%s - %s', $sectionLabel, $objectTitle);
		}

		$subject = sprintf($subjectTemplate, $primaryTitle);

		$users = $this->_parseUsers($shareData['Aro']['foreign_key']);
		foreach ($users as $userId) {
			//check if notification should be send
			list($plugin, $name) = pluginSplit($this->_parentModel);
			$notify = $this->{$name}->Behaviors->VisualisationLog->isReadyToNotify(
				$this->{$name},
				[$object['Aco']['model'], $object['Aco']['foreign_key']],
				$userId
			);

			if (!$notify) {
				continue;
			} 

			$User = ClassRegistry::init('User');
			$User->id = $userId;
			$user = $User->find('first', [
				'conditions' => [
					'User.id' => $userId
				],
				'recursive' => -1
			]);

			$email = AppModule::instance('Visualisation')->email([
				'to' => $user['User']['email'],
				'subject' => $subject,
				'template' => $template,
				'viewVars' => [
					'shareData' => $shareData,
					'user' => $user,
					'whoShared' => $this->currentUser('full_name'),
					'object' => $object,
					'sectionLabel' => $sectionLabel,
					'objectTitle' => $primaryTitle,
					'gatewayUrl' => [
						'plugin' => 'visualisation',
						'controller' => 'visualisation',
						'action' => 'redirectGateway',
						$this->alias,
						$id
					]
				]
			]);

			$ret &= (bool) $email->send();
		}

		return $ret;
	}

	/**
	 * Get ARO foreign key of an object.
	 */
	protected function _getAroId($aroId) {

	}

	protected function _parseUsers($permissionForeignKey) {
		return (array) $permissionForeignKey;
	}

	/**
	 * Get joins array for ACL accessibility.
	 * 
	 * @return array Query joins parameter.
	 */
	public function getJoins() {
		return [
			0 =>[
				'table' => 'aros_acos',
				'alias' => 'Permission',
				'type' => 'INNER',
				'conditions' => [
					$this->alias . '.aros_acos_id = Permission.id'
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
		];
	}

	// find existing rows by $model and $foreignKey
	public function findExisting($model, $foreignKey = null) {
		$data = $this->find('all', [
			'conditions' => [
				'Aco.model' => $model,
				'Aco.foreign_key' => $foreignKey
			],
			'joins' => $this->getJoins(),
			'recursive' => -1
		]);
		
		return $data;
	}

	public function getSharedObject($permissionId) {
		$data = $this->Permission->find('first', [
			'conditions' => [
				'Permission.id' => $permissionId
			],
			'fields' => [
				'Aco.model', 'Aco.foreign_key'
			],
			'joins' => [
				[
					'table' => 'acos',
					'alias' => 'Aco',
					'type' => 'INNER',
					'conditions' => [
						'Permission.aco_id = Aco.id'
					]
				]
			],
			'recursive' => -1
		]);

		return $data;
	}

}
