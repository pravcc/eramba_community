<?php
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

class Group extends AppModel {
	const ADMIN_ID = ADMIN_GROUP_ID;

	public $displayField = 'full_name_with_type';

	public $name = 'Group';
	public $actsAs = array(
		'Acl' => array('type' => 'requester'),
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description'
			)
		),
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'Name is required.'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Same group already exists.'
			)
		)
	);

	public $hasAndBelongsToMany = array(
		'User' => array(
			'with' => 'UsersGroup',
			'className' => 'User',
			'joinTable' => 'users_groups',
			'foreignKey' => 'group_id',
			'associationForeignKey' => 'user_id'
		)
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Groups');
		$this->_group = self::SECTION_GROUP_SYSTEM;
		
		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'description' => __('Give a name to this group'),
				'editable' => true
			],
			'description' => [
				'label' => __('Description'),
				'description' => __('Give a description to this group'),
				'editable' => true
			],
			'access_list' => [
				'label' => __('Access List'),
				'editable' => false
			]
		];

		$this->advancedFilterSettings = [
            'use_new_filters' => true
		];
		
		parent::__construct($id, $table, $ds);

		$this->virtualFields['full_name_with_type'] = "CONCAT(`{$this->alias}`.`name`, ' ', '(" . __('Group') . ")')";
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->textField('name', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->nonFilterableField('access_list');

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function parentNode($type) {
		return null;
	}

	/**
	 * Reset ACL cache after a group has been saved.
	 */
	public function afterSave($created, $options = array()) {
		$ret = true;
		if ($created) {
			$CustomRolesGroup = ClassRegistry::init('CustomRoles.CustomRolesGroup');
			$ret &= $CustomRolesGroup->syncSingleObject($this->id);

			$Permission = ClassRegistry::init('AppPermission');

			// get aro<->aco links
			$link = $Permission->getAclLink($this, 'controllers');

			// if there is no link between this aro and 'controllers' aco, create it
			if (empty($link['link'])) {
				// $ret &= $Permission->allow($this, 'controllers');
			}

			// when permission setup is triggered for admin group, allow everything
			if ($this->id === self::ADMIN_ID) {
				$ret &= $Permission->allow($this, 'visualisation');
			}
			// other groups by default can create new objects (allow 'create' on model node)
			else {
				$ret &= $Permission->allow($this, 'models', 'create');
			}
		}

		$Setting = ClassRegistry::init('Setting');
		$Setting->deleteCache('acl');

		return $ret;
	}

	/**
	 * Restrict deletion if a Connector is still in use.
	 */
	public function beforeDelete($cascade = true) {
		$ret = true;
		if ($this->id == ADMIN_GROUP_ID) {
			$ret = false;
			$this->customDeleteMessage = __('Admin group user cannot be deleted.');
		} else if ($this->hasUsers($this->id)) {
			$ret = false;
			$this->customDeleteMessage = __('Group cannot be deleted because it contains one or more users.');
		} else {
			$ret = $this->_replaceUserFields();
		}

		return $ret;
	}

	public function _replaceUserFields()
	{
		$UserFieldsUser = ClassRegistry::init('UserFields.UserFieldsUser');
		$UserFieldsGroup = ClassRegistry::init('UserFields.UserFieldsGroup');

		$replaceList = $UserFieldsGroup->find('all', [
			'conditions' => [
				'UserFieldsGroup.group_id' => $this->id,
				'UserFieldsGroup.model !=' => [
					'VisualisationShareUser',
					'VisualisationShareGroup',
					'VisualisationShare'
				]
			],
			'fields' => [
				'model',
				'foreign_key',
				'field'
			],
			'recursive' => -1
		]);
		
		$ret = true;
		foreach ($replaceList as $item) {
			$fieldName = substr($item['UserFieldsGroup']['field'], 0, -5);

			$allGroups = $UserFieldsGroup->find('list', [
				'conditions' => [
					'UserFieldsGroup.model' => $item['UserFieldsGroup']['model'],
					'UserFieldsGroup.foreign_key' => $item['UserFieldsGroup']['foreign_key'],
					'UserFieldsGroup.field' => $item['UserFieldsGroup']['field']
				],
				'fields' => [
					'group_id', 'group_id'
				],
				'recursive' => -1
			]);

			$allUsers = $UserFieldsUser->find('list', [
				'conditions' => [
					'UserFieldsUser.model' => $item['UserFieldsGroup']['model'],
					'UserFieldsUser.foreign_key' => $item['UserFieldsGroup']['foreign_key'],
					'UserFieldsUser.field' => $fieldName
				],
				'fields' => [
					'user_id', 'user_id'
				],
				'recursive' => -1
			]);

			unset($allGroups[$this->id]);
			$allGroups[] = ADMIN_GROUP_ID;
			$allGroups = array_unique($allGroups);

			$userFieldData = [];
			foreach ($allGroups as $group) {
				$userFieldData[] = 'Group-' . $group;
			}

			foreach ($allUsers as $user) {
				$userFieldData[] = 'User-' . $user;
			}

			$saveData = [
                $item['UserFieldsGroup']['model'] => [
                    'id' => $item['UserFieldsGroup']['foreign_key'],
                    $fieldName => $userFieldData
                ]
            ];

            $M = ClassRegistry::init($item['UserFieldsGroup']['model']);
            if ($M->Behaviors->loaded('Utils.SoftDelete')) {
            	$M->Behaviors->disable('Utils.SoftDelete');
            }

            $ret &= $M->saveAssociated($saveData, [
                'validate' => 'first',
                'atomic' => true,
                'deep' => true,
                'fieldList' => [
                	$fieldName
                ]
            ]);

            if ($M->Behaviors->loaded('Utils.SoftDelete')) {
            	$M->Behaviors->enable('Utils.SoftDelete');
            }
		}

		return $ret;
	}

	/**
	 * Checks if a Group contains any users.
	 */
	public function hasUsers($id) {
		$users = $this->User->find('all', array(
			'contain' => array(
				'Group'
			),
			'recursive' => -1
		));

		$ret = false;
		foreach ($users as $user) {
			$groups = Hash::extract($user, 'Group.{n}.id');
			
			if (in_array($id, $groups)) {
				$ret = true;
			}
		}

		return $ret;
	}
}
