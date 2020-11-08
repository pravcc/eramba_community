<?php
class NotificationSystem extends AppModel {
	public $useTable = 'notification_system_items';
	private $customLogData = array(
		'created' => false,
		'updated' => false
	);

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'model', 'filename', 'email_subject', 'email_body'
			)
		)
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'type' => array(
			'rule' => array('inList', array(
				NOTIFICATION_TYPE_AWARENESS,
				NOTIFICATION_TYPE_WARNING,
				NOTIFICATION_TYPE_DEFAULT,
				NOTIFICATION_TYPE_REPORT
			)),
			'required' => true,
			'allowEmpty' => false
		),
		'filename' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'You must choose a notification.'
		),
		'emails' => array(
			'checkEmails' => array(
				'rule' => array('checkEmails'),
				'required' => true,
				'allowEmpty' => true,
				'message' => 'You entered a wrongly formatted email address.'
			)
		),
		/*'user_id' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'You must choose at least one user.'
			)
		),*/
		'feedback' => array(
		),
		'chase_interval' => array(
			'naturalNumber' => array(
				'rule' => 'naturalNumber',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter a number.'
			),
			'range' => array(
				'rule' => array('range', 0, 7),
				'message' => 'Please enter a number between 1 and 6.'
			)
		),
		'chase_amount' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter a number.'
			),
			'range' => array(
				'rule' => array('range', -1, 15),
				'message' => 'Please enter a number between 0 and 15.'
			)
		),
		'trigger_period' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please fill in this field.'
			),
			'range' => array(
				'rule' => array('range', -1, 366),
				'message' => 'Please enter a number between 0 and 365.'
			)
		),
		'email_subject' =>  array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please fill in this field.'
			)
		),
		'email_body' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please fill in this field.'
			)
		)
	);

	/*
	 * static enum: Model::function()
	 * @access static
	 */
	 public static function reportEmptyResultTypes($value = null) {
		$options = array(
			self::REPORT_SEND_EMPTY_RESULTS_DISABLE => __('Skip Empty Results'),
			self::REPORT_SEND_EMPTY_RESULTS_ENABLE => __('Send Empty Results')
		);
		return parent::enum($value, $options);
	}
	const REPORT_SEND_EMPTY_RESULTS_DISABLE = 0;
	const REPORT_SEND_EMPTY_RESULTS_ENABLE = 1;

	/*
	 * static enum: Model::function()
	 * @access static
	 */
	 public static function reportAttachmentTypes($value = null) {
		$options = array(
			self::REPORT_ATTACHEMENT_BOTH => __('PDF and CSV'),
			self::REPORT_ATTACHEMENT_PDF => __('PDF'),
			self::REPORT_ATTACHEMENT_CSV => __('CSV'),
		);
		return parent::enum($value, $options);
	}
	const REPORT_ATTACHEMENT_BOTH = 0;
	const REPORT_ATTACHEMENT_PDF = 1;
	const REPORT_ATTACHEMENT_CSV = 2;


	public function beforeValidate($options = array()) {
		// set the default configuration for saveMany validation purposes
		$this->validator()
			->add('chase_interval', $this->validate['chase_interval'])
			->add('chase_amount', $this->validate['chase_amount'])
			->add('trigger_period', $this->validate['trigger_period'])
			->add('email_subject', $this->validate['email_subject'])
			->add('email_body', $this->validate['email_body']);

		if (empty($this->data['NotificationSystem']['feedback'])) {
			$this->validator()->remove('chase_interval');
			$this->validator()->remove('chase_amount');
		}

		if (!in_array($this->data['NotificationSystem']['type'], array(NOTIFICATION_TYPE_REPORT, NOTIFICATION_TYPE_AWARENESS))) {
			$this->validator()->remove('trigger_period');
		}

		// customized email validation
		if (empty($this->data['NotificationSystem']['email_customized'])) {
			$this->validator()->remove('email_subject');
			$this->validator()->remove('email_body');
		}

		// in case this notification is a Report notification
		if ($this->data['NotificationSystem']['type'] == NOTIFICATION_TYPE_REPORT) {
			$this->addListValidation('report_attachment_type', array_keys(self::reportAttachmentTypes()));
			$this->addListValidation('report_send_empty_results', array_keys(self::reportEmptyResultTypes()));

			$this->validator()->remove('chase_interval');
			$this->validator()->remove('chase_amount');
		}
	}

	public function checkEmails($val = null) {
		$emails = explode(',', $val['emails']);

		$ret = true;
		foreach ($emails as $email) {
			$ret &= Validation::email($email);
		}

		return $ret;
	}

	public $hasMany = array(
		'NotificationObject',
		'NotificationEmail' => array(
			'className' => 'NotificationSystemItemEmail'
		),
		'NotificationCustom' => array(
			'className' => 'NotificationSystemItemCustomRole'
		),
		/*'NotificationCustomUser' => array(
			'className' => 'NotificationSystemItemCustomUser'
		)*/
	);

	public $hasAndBelongsToMany = array(
		'NotificationUser' => array(
			'className' => 'User',
			'with' => 'NotificationSystemItemsUser',
			'joinTable' => 'notification_system_items_users',
			'fields' => array('id', 'email')
		),
		'NotificationScope' => array(
			'className' => 'User',
			'with' => 'NotificationSystemItemsScope',
			'joinTable' => 'notification_system_items_scopes',
			'fields' => array('id', 'email')
		)
	);

	public function afterSave($created, $options = array()) {
		if (($created && $this->customLogData['created']) || (!$created && $this->customLogData['updated'])) {
			return true;
		}

		if (!isset($this->data['NotificationSystem']['model'])) {
			return true;
		}
		
		// $log = ClassRegistry::init($this->data['NotificationSystem']['model']);

		if (!$created) { 
			$options = array(
				'titleSuffix' => ' <b>(' . __('Notifications updated') . ')</b>'
			);

			$this->customLogData['updated'] = true;
		}

		if ($created && !$this->customLogData['created']) { 
			$options = array(
				'titleSuffix' => ' <b>(' . __('Notifications created') . ')</b>'
			);

			$this->customLogData['created'] = true;
		}

		// $ret = $log->setSystemRecord($this->data['NotificationSystem']['foreign_key'], 2, $options);

		return true;
	}

	/**
	 * Query data in database and format results for usage in view.
	 * 
	 * @param  string $model Model.
	 * @return array        Formatted results.
	 */
	public function readNotifications($model) {
		$this->bindWorkflow();
		$data = $this->find('all', array(
			'conditions' => array(
				'NotificationSystem.model' => $model
			),
			'contain' => array(
				'NotificationObject',
				'NotificationEmail',
				'NotificationUser',
				'NotificationScope',
				'NotificationCustom',
				'Workflow'
			)
		));

		$ret = array();
		if (!empty($data)) {
			$ret['NotificationSystem'] = array();

			foreach ($data as $item) {
				/*$objectIds = array();
				foreach ($item['NotificationObject'] as $object) {
					$objectIds[] = $object['foreign_key'];
				}*/

				$emails = array();
				foreach ($item['NotificationEmail'] as $email) {
					$emails[] = $email['email'];
				}

				$userIds = array();
				foreach ($item['NotificationUser'] as $user) {
					$userIds[] = $user['id'];
				}

				$scopeIds = array();
				foreach ($item['NotificationScope'] as $scope) {
					$scopeIds[] = $scope['NotificationSystemItemsScope']['custom_identifier'];
				}

				$customRoles = array();
				foreach ($item['NotificationCustom'] as $custom) {
					$customRoles[] = $custom['custom_identifier'];
				}

				$ret['NotificationSystem'][] = am($item['NotificationSystem'], array(
					'emails' => implode(',', $emails),
					//'object_id' => $objectIds,
					'user_id' => $userIds,
					'scope_id' => $scopeIds,
					'custom_roles' => $customRoles
				));
			}
		}

		return $ret;
	}

	public function clearNotifications($model, $id) {
		return $this->deleteAll(array(
			'NotificationSystem.model' => $model,
			'NotificationSystem.foreign_key' => $id
		));
	}

	/**
	 * Saves all notifications for a section.
	 */
	public function saveNotifications($model) {
		$ret = true;
		
		foreach ($this->data['NotificationSystem'] as $item) {
			$saveData = am($item, array('model' => $model));

			$this->clear();

			if (isset($item['id'])) {
				$this->id = $item['id'];

				$ret &= $this->NotificationSystemItemsUser->deleteAll(array(
					'NotificationSystemItemsUser.notification_system_item_id' => $this->id
				));

				$ret &= $this->NotificationEmail->deleteAll(array(
					'NotificationEmail.notification_system_item_id' => $this->id
				));

				$ret &= $this->NotificationSystemItemsScope->deleteAll(array(
					'NotificationSystemItemsScope.notification_system_item_id' => $this->id
				));

				$ret &= $this->NotificationCustom->deleteAll(array(
					'NotificationCustom.notification_system_item_id' => $this->id
				));
			}
			else {
				$this->create();
			}

			$this->set($saveData);
			$ret &= $_savedData = $this->save($saveData, false);

			//for a Report notification, we automatically save a NotificationObject having foreign_key NULL
			if ($_savedData['NotificationSystem']['type'] == NOTIFICATION_TYPE_REPORT) {
				$ret &= $this->NotificationObject->associate(
					$_savedData['NotificationSystem']['id'],
					$_savedData['NotificationSystem']['model'],
					null
				);
			}

			if (!empty($item['user_id'])) {
				$userData = array();
				foreach ($item['user_id'] as $user_id) {
					$userData[] = array(
						'notification_system_item_id' => $this->id,
						'user_id' => $user_id
					);
				}

				if (!empty($userData)) {
					$ret &= $this->NotificationSystemItemsUser->saveMany($userData, array('validate' => false));
				}
			}

			if (!empty($item['emails'])) {
				$customEmails = explode(',', $item['emails']);
				$emailData = array();
				foreach ($customEmails as $email) {
					$emailData[] = array(
						'notification_system_item_id' => $this->id,
						'email' => $email
					);
				}

				if (!empty($emailData)) {
					$ret &= $this->NotificationEmail->saveMany($emailData, array('validate' => false));
				}
			}

			if (!empty($item['custom_roles'])) {
				$customRoles = array();
				foreach ($item['custom_roles'] as $role) {
					$customRoles[] = array(
						'notification_system_item_id' => $this->id,
						'custom_identifier' => $role,

						// this is to store info into the row that its resaved and using new custom roles
						'migration_updated' => 1
					);
				}

				if (!empty($customRoles)) {
					$ret &= $this->NotificationCustom->saveMany($customRoles, array('validate' => false));
					$ret &= $this->saveCustomUsers($this->id);
				}
				
			}
			else {
				$notificationObjectIds = $this->NotificationObject->find('list', array(
					'conditions' => array(
						'NotificationObject.notification_system_item_id' => $this->id
					),
					'fields' => array('id', 'id'),
					'recursive' => -1
				));

				$ret &= $this->NotificationObject->NotificationCustomUser->deleteAll(array(
					'NotificationCustomUser.notification_system_item_object_id' => $notificationObjectIds
				));
			}

			if (!empty($item['scope_id'])) {
				$scopeClass = ClassRegistry::init('Scope');
				$scope = $scopeClass->find('first');
				$scopeData = array();
				foreach ($item['scope_id'] as $id) {
					$scopeData[] = array(
						'user_id' => $scope['Scope'][$id . '_id'],
						'custom_identifier' => $id,
						'notification_system_item_id' => $this->id
					);
				}

				if (!empty($scopeData)) {
					$ret &= $this->NotificationSystemItemsScope->saveMany($scopeData, array('validate' => false));
				}
			}
			
		}

		return $ret;
	}

	public function saveCustomUsersByModel($model, $foreign_key) {
		$data = $this->NotificationObject->find('list', array(
			'conditions' => array(
				'NotificationObject.model' => $model,
				'NotificationObject.foreign_key' => $foreign_key
			),
			'fields' => array('NotificationObject.id', 'NotificationObject.notification_system_item_id'),
			// 'group' => 'NotificationObject.notification_system_item_id',
			'recursive' => -1
		));

		$ids = array_unique(array_values($data));
		$objects = array_keys($data);

		$ret = true;
		foreach ($ids as $notificationSystemId) {
			$ret &= $this->saveCustomUsers($notificationSystemId, array(
				'notificationObjectIds' => $objects
			));
		}

		return $ret;
	}

	public function saveCustomUsers($notificationSystemId, $options = array()) {
		$options = am(array(
			'notificationObjectIds' => null
		), $options);

		$objectConds = array();
		if (!empty($options['notificationObjectIds'])) {
			$objectConds['NotificationObject.id'] = $options['notificationObjectIds'];
		}

		$data = $this->find('first', array(
			'conditions' => array(
				'NotificationSystem.id' => $notificationSystemId
			),
			'contain' => array(
				'NotificationCustom' => [
					'conditions' => [
						// lets handle only up to date formatted custom roles data
						'NotificationCustom.migration_updated' => 1
					]
				],
				'NotificationObject' => array(
					'conditions' => $objectConds
				)
			)
		));

		if (empty($data['NotificationCustom'])) {
			return true;
		}

		/*$this->NotificationCustomUser->bindModel(array(
			'belongsTo' => array(
				'NotificationObject' => array(
					'foreignKey' => 'notification_system_item_object_id'
				)
			)
		));*/

		$ret = true;

		// delete custom users for the entire set of notification objects before updating 
		$notificationObjectsToDelete = Hash::extract($data, 'NotificationObject.{n}.id');
		$ret &= $this->NotificationObject->NotificationCustomUser->deleteAll(array(
			'NotificationCustomUser.notification_system_item_object_id' => $notificationObjectsToDelete
		));

		$CustomRolesUsers = ClassRegistry::init('CustomRoles.CustomRolesUsers');
		$CustomRolesGroups = ClassRegistry::init('CustomRoles.CustomRolesGroups');

		// formatted array where key is notification object ID and value is array of user IDs
		$users = $CustomRolesUsers->parseCustomCompatible(
			$data['NotificationSystem']['model'],
			$data['NotificationCustom'],
			$data['NotificationObject']
		);

		$users2 = $CustomRolesGroups->parseCustomCompatible(
			$data['NotificationSystem']['model'],
			$data['NotificationCustom'],
			$data['NotificationObject']
		);

		$users = Hash::merge($users, $users2);

		foreach ($users as $key => $list) {
			$users[$key] = array_unique($list);
		}
		
		// exit;
		// return true;
		// debug($data);exit;
		// $this->bindWorkflow();
		// $users = $this->Workflow->parseCustomData($data['NotificationCustom'], false, $data['NotificationSystem']['model'], 'notifications');

		$customUsers = array();
		foreach ($users as $notificationObjectId => $usersArr) {
			foreach ($usersArr as $userId) {
				$customUsers[] = [
					'notification_system_item_object_id' => $notificationObjectId,
					'user_id' => $userId
				];
			}
		}

		/*foreach ($data['NotificationObject'] as $item) {
			$model = $item['model'];

			$checkDuplicates = array();
			foreach ($users as $key => $user) {
				if ($user['foreign_key'] == $item['foreign_key'] && !empty($user['user_id'])) {
					//check duplicate ocurrence and skip it if thats the case
					if (in_array($user['user_id'], $checkDuplicates)) {
						continue;
					}

					$customUsers[] = array(
						'notification_system_item_object_id' => $item['id'],
						'user_id' => $user['user_id']
					);

					$checkDuplicates[] = $user['user_id'];
				}
			}
		}*/

		if (!empty($customUsers)) {
			$ret &= $this->NotificationObject->NotificationCustomUser->saveMany($customUsers, array('validate' => false, 'atomic' => false));
		}

		return $ret;
	}

	private function getCustomUsers($notificationId, $customRoles, $model, $id) {
		// debug($customRoles);exit;
		// $this->bindWorkflow();
		// $users = $this->Workflow->parseCustomData($customRoles, false, $model, 'notifications');
		$users = $CustomRoleModel->parseCustomCompatible($model, $customRoles);

		// debug($users);exit;
		$customUsers = array();
		foreach ($users as $key => $user) {
			if ($user['foreign_key'] == $id && !empty($user['user_id'])) {
				//check duplicate ocurrence and skip it if thats the case
				$usedIds = Hash::extract($customUsers, '{n}.user_id');
				if (in_array($user['user_id'], $usedIds)) {
					continue;
				}

				$customUsers[] = array(
					'notification_system_item_object_id' => $notificationId,
					'user_id' => $user['user_id']
				);
			}
		}

		return $customUsers;
	}

	private function bindWorkflow() {
		$this->bindModel(array(
			'belongsTo' => array(
				'Workflow' => array(
					'foreignKey' => false,
					'conditions' => array(
						'NotificationSystem.model = Workflow.model'
					)
				)
			)
		));
	}

	public function hasNotifications($model, $foreign_key) {
		$item = $this->NotificationSystem->find('first', array(
			'conditions' => array(
				'NotificationSystem.model' => $model,
				'NotificationSystem.foreign_key' => $foreign_key
			),
			'fields' => array('email_notification', 'header_notification'),
			'recursive' => -1
		));

		return array(
			'email' => $item['NotificationSystem']['email_notification'],
			'header' => $item['NotificationSystem']['header_notification']
		);
	}

	/**
	 * Associated all automated notifications to a cetrain item.
	 */
	public function associateForAutomated($model, $foreign_key) {
		$automatedNotifications = $this->find('list', array(
			'conditions' => array(
				'NotificationSystem.automated' => 1,
				'NotificationSystem.model' => $model
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		$ret = true;
		if (!empty($automatedNotifications)) {
			foreach ($automatedNotifications as $notificationId) {
				$ret &= $this->NotificationObject->associate($notificationId, $model, $foreign_key);
			}
		}

		return $ret;
	}


}
