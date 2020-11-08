<?php

App::uses('Component', 'Controller');

/**
 * Custom Roles component that attaches into the ACL object access checking process.
 */
class CustomRolesComponent extends Component {
	public $components = ['Session', 'Visualisation', 'Acl'];
	public $settings = [
		'enabled' => true
	];

	// temporary solution to store user's groups within a request
	protected $_usersGroupsCache = [];

	/**
	 * Runtime configuration values.
	 * 
	 * @var array
	 */
	protected $_runtime = [];

	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (empty($this->settings)) {
			$this->settings = array(
			);
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);
	}

	public function initialize(Controller $controller) {
		parent::initialize($controller);
		$this->controller = $controller;

		$this->CustomRolesUser = ClassRegistry::init('CustomRoles.CustomRolesUser');
		$this->CustomRolesGroup = ClassRegistry::init('CustomRoles.CustomRolesGroup');
		
		$this->_storeCustomData();
	}

	/**
	 * Stores internally data about all custom users and groups in the app.
	 */
	protected function _storeCustomData() {
		if (($data = Cache::read('custom_roles_data', 'custom_roles')) === false) {
			$data = $this->getCustomRoleData();
			Cache::write('custom_roles_data', $data, 'custom_roles');
		}

		$this->_runtime = $data;
	}

	/**
	 * Get custom role users which is then stored internally into runtime variable for later use in ACL checks.
	 * Formatted as user_id (or group_id) => primary_id (which then easily points to relevant ARO node)
	 */
	public function getCustomRoleData() {
		$data['users'] = $this->CustomRolesUser->find('list', [
			'fields' => [
				'CustomRolesUser.user_id', 'CustomRolesUser.id'
			],
			'recursive' => -1
		]);

		$data['groups'] = $this->CustomRolesGroup->find('list', [
			'fields' => [
				'CustomRolesGroup.group_id', 'CustomRolesGroup.id'
			],
			'recursive' => -1
		]);

		return $data;
	}

	// visualisation event callback
	public function onCheck($requestor, Model $model, $object, $action) {
		// object for which we are checkng permission access
		$objectNode = [
			$model->alias => [
				$model->primaryKey => $object
			]
		];

		$customRolesUserId = $this->_runtime['users'][$requestor];
		// $this->CustomRolesUser->id = $customRolesUserId;

		$requestorNode = [
			'CustomRoles.CustomRolesUser' => [
				'id' => $customRolesUserId
			]
		];

		// find groups
		if (!isset($this->_usersGroupsCache[$requestor])) {
			$userGroups = ClassRegistry::init('UsersGroup')->find('list', [
				'conditions' => [
					'UsersGroup.user_id' => $requestor
				],
				'fields' => [
					'UsersGroup.group_id'
				],
				'recursive' => -1
			]);

			$this->_usersGroupsCache[$requestor] = $userGroups;
		} else {
			$userGroups = $this->_usersGroupsCache[$requestor];
		}

		// lets search for primary IDs of custom role groups to use for searching ARO nodes afterwards
		$customRolesGroupIds = [];
		foreach ($userGroups as $groupId) {
			if (isset($this->_runtime['groups'][$groupId])) {
				$customRolesGroupIds[] = $this->_runtime['groups'][$groupId];
			}
		}

		$groupNode = [
			'CustomRoles.CustomRolesGroup' => [
				'id' => $customRolesGroupIds
			]
		];

		$ret = $this->Acl->check($requestorNode, $objectNode, $action);
		$ret = $ret || $this->Acl->check($groupNode, $objectNode, $action);

		if ($ret) {
			return true;
		}

		return null;
	}
	
}
