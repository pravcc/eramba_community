<?php
App::uses('ModuleBase', 'Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('LdapConnector', 'Model');
App::uses('LdapSynchronization', 'LdapSync.Model');
App::uses('User', 'Model');
App::uses('NotificationSystemSubject', 'NotificationSystem.Lib');
App::uses('Hash', 'Utility');

class LdapSyncModule extends ModuleBase
{
	/**
	 * LdapSynchronization model
	 * @var LdapSynchronization
	 */
	protected $LdapSynchronization = null;

	/**
	 * LdapConnector model
	 * @var LdapConnector
	 */
	protected $LdapConnector = null;

	/**
	 * User model
	 * @var User
	 */
	protected $User = null;

	const SYNC_RESULT_SUCCESS = 'success';
	const SYNC_RESULT_FAILURE = 'failure';

	/**
	 * @var array
	 */
	protected $_userStatuses = [
		// 'userLogin1' => [
		// 	'login' => '',
		// 	'message' => '',
		// 	'status' => '',
		// 	'data' => [],
		// 	'additional_data' => []
		// ]
	];

	public function __construct()
	{
		parent::__construct();

		$this->LdapSynchronization = ClassRegistry::init('LdapSync.LdapSynchronization');
		$this->LdapConnector = ClassRegistry::init('LdapConnector');
		$this->User = ClassRegistry::init('User');
	}

	public function synchronizeAll()
	{
		$synchronizations = $this->LdapSynchronization->find('all', [
			'conditions' => [
				'LdapSynchronization.status' => LdapSynchronization::STATUS_ACTIVE
			],
			'order' => [
				sprintf("(CASE LdapSynchronization.no_user_action WHEN %s THEN 1 WHEN %s THEN 2 WHEN %s THEN 3 END)", LdapSynchronization::NO_USER_ACTION_DELETE, LdapSynchronization::NO_USER_ACTION_DISABLE, LdapSynchronization::NO_USER_ACTION_IGNORE) => 'ASC'
			],
			'recursive' => -1
		]);

		$data = [];

		if (!empty($synchronizations)) {
			foreach ($synchronizations as $sync) {
				$data[$sync['LdapSynchronization']['id']] = $this->syncLdapUsers($sync['LdapSynchronization']['id'], true, 500);
			}
		}

		return $data;
	}

	/**
	 * Synchronize LDAP and eramba users
	 * @param  int   $id       ID of LDAP synchronization
	 * @param  bool  $saveToDb Whether or not to save results to db or just return it as an array
	 * @param  int   $limit    Set how many users should be synchronized
	 * @return array           Users who were synchronized
	 */
	public function syncLdapUsers($id, bool $saveToDb = true, int $limit = null)
	{
		$syncResult = "";
		$syncResultMsg = "";

		//
		// Get logins from all ldap groups which are synchronized automatically
		$ldapSynchronizations = $this->LdapSynchronization->find('all', [
			'conditions' => [
				'LdapSynchronization.status' => LdapSynchronization::STATUS_ACTIVE
			],
			'order' => [
				'LdapSynchronization.id' => 'DESC'
			],
			'contian' => [
				'Group', 'Portal'
			]
		]);

		$allLdapGroupsLogins = [];
		$allSyncUserPortals = [];
		$allSyncUserGroups = [];
		if (!empty($ldapSynchronizations)) {
			foreach ($ldapSynchronizations as $LdapSynchronization) {
				$tempData = $LdapSynchronization['LdapSynchronization'];
				$groupConnId = $tempData['ldap_group_connector_id'];
				$authConnId = $tempData['ldap_auth_connector_id'];

				// Get list of users from LDAP group
				$groupUsersList = $this->listLdapUsers($groupConnId, $tempData['ldap_group']);

				// Find all valid users in LDAP server from given list of users
				$validUsersData = $this->getLdapUsersData($authConnId, $groupUsersList);

				foreach ($validUsersData as $usrDt) {
					if ($usrDt['valid'] == true && !in_array($usrDt['data']['login'], $allLdapGroupsLogins, true)) {
						$allLdapGroupsLogins[] = $usrDt['data']['login'];
					}

					if ($usrDt['valid'] == true) {
						//
						// Portals
						if (!isset($allSyncUserPortals[$usrDt['data']['login']])) {
							$allSyncUserPortals[$usrDt['data']['login']] = [];
						}

						foreach ($LdapSynchronization['Portal'] as $portal) {
							if (!in_array($portal['id'], $allSyncUserPortals[$usrDt['data']['login']])) {
								$allSyncUserPortals[$usrDt['data']['login']][] = $portal['id'];
							}
						}
						// 
						
						//
						// Groups
						if (!isset($allSyncUserGroups[$usrDt['data']['login']])) {
							$allSyncUserGroups[$usrDt['data']['login']] = [];
						}

						foreach ($LdapSynchronization['Group'] as $group) {
							if (!in_array($group['id'], $allSyncUserGroups[$usrDt['data']['login']])) {
								$allSyncUserGroups[$usrDt['data']['login']][] = $group['id'];
							}
						}
						// 
					}
				}
			}
		}
		// 
		
		$ldapSyncData = $this->LdapSynchronization->find('first', [
			'conditions' => [
				'LdapSynchronization.id' => $id
			]
		]);

		$data = isset($ldapSyncData['LdapSynchronization']) ? $ldapSyncData['LdapSynchronization'] : [];

		$ldapGroupExists = false;
		$usersResults = [];
		if (!empty($ldapSyncData)) {
			$ldapAuthConnectorId = intval($data['ldap_auth_connector_id']);
			$ldapGroupConnectorId = intval($data['ldap_group_connector_id']);
			$ldapGroup = $data['ldap_group'];

			$allLdapGroups = $this->getLdapGroupsList($ldapGroupConnectorId);
			if (in_array($ldapGroup, $allLdapGroups, true)) {
				$ldapGroupExists = true;
			}

			// Get list of users from given LDAP group
			$users = $this->listLdapUsers($ldapGroupConnectorId, $ldapGroup);

			// Find all valid users in LDAP server
			$results = $this->getLdapUsersData($ldapAuthConnectorId, $users);

			$groups = $ldapSyncData['Group'];
			$portals = $ldapSyncData['Portal'];

			$defaultUserData = [
				'name' => '',
				'surname' => '',
				'email' => '',
				'login' => '',
				'local_account' => 0,
				'ldap_sync' => 1,
				'LdapSynchronization' => [$id],
				'Portal' => Hash::extract($portals, "{n}.id"),
				'Group' => Hash::extract($groups, "{n}.id"),
				'language' => 'eng',
				'status' => User::STATUS_ACTIVE,
				'blocked' => 0,
				'api_allow' => 0
			];

			$usersData = [];
			foreach ($results as $result) {
				if ($result['valid']) {
					$ldapUserData = $result['data'];
					
					$usersData[] = array_merge($defaultUserData, [
						'login' => $ldapUserData['login'],
						'name' => $ldapUserData['name'],
						'surname' => $ldapUserData['surname'],
						'email' => $ldapUserData['email']
					]);
				}
			}

			$erambaUsers = $this->User->find('all', [
				'fields' => [
					'User.id', 'User.login', 'User.status', 'User.local_account', 'ldap_sync'
				],
				'contain' => [
					'Portal', 'Group', 'LdapSynchronization'
				]
			]);

			$erambaUsersTemp = [];
			foreach ($erambaUsers as $eu) {
				$userLogin = $eu['User']['login'];
				$erambaUsersTemp[$userLogin]['User'] = $eu['User'];
				$erambaUsersTemp[$userLogin]['portals'] = Hash::extract($eu['Portal'], '{n}.id');
				$erambaUsersTemp[$userLogin]['groups'] = Hash::extract($eu['Group'], '{n}.id');
				$erambaUsersTemp[$userLogin]['ldap_synchronizations'] = Hash::extract($eu['LdapSynchronization'], '{n}.id');
				$erambaUsersTemp[$userLogin]['no_user_actions'] = Hash::extract($eu['LdapSynchronization'], '{n}.no_user_action');
			}
			$erambaUsers = $erambaUsersTemp;

			//
			// Find users which we need to remove, disable or ignore in eramba
			foreach ($erambaUsers as $erambaUser) {
				$erambaUserData = $erambaUser['User'];
				// Skip admin and all local and previously manually added ldap users
				if ($erambaUserData['id'] == ADMIN_ID ||
					$erambaUserData['local_account'] == User::LOCAL_ACCOUNT ||
					empty($erambaUserData['ldap_sync']) ||
					!in_array($data['id'], $erambaUser['ldap_synchronizations'])) {

					if (in_array($erambaUserData['login'], $allLdapGroupsLogins, true)
						&& $erambaUserData['local_account'] == User::LOCAL_ACCOUNT
					) {
						$usr['action-desc'] = 'skip-ignore-local';
						$usr['action'] = 'skip';
						$usr['actionMsg'] = __('Already exists locally');
						$usr['login'] = $erambaUserData['login'];
						$usr['data'] = [
							'id' => $erambaUserData['id'],
							'status' => $erambaUserData['status']
						];

						$this->_setUserStatus($erambaUserData['login'], 'local', __(
							'Already exists locally'
						));

						$usersResults[] = $usr;
					}
					
					continue;
				}

				if (!in_array($erambaUserData['login'], $allLdapGroupsLogins, true)) {
					$usr = [
						'login' => $erambaUserData['login']
					];

					$usrAction = '';
					$usrActionMsg = '';
					if (in_array(LdapSynchronization::NO_USER_ACTION_IGNORE, $erambaUser['no_user_actions'])) {
						$usrAction = 'skip';
						$usrActionMsg = __('User will be ignored');
						$usr['action-desc'] = 'skip-ignore';

						$this->_setUserStatus($erambaUserData['login'], 'remove-ignore', __(
							'User is not in the group and will be ignored according to sync settings'
						));

					} elseif (in_array(LdapSynchronization::NO_USER_ACTION_DISABLE, $erambaUser['no_user_actions'])) {
						if ($erambaUserData['status'] == User::STATUS_NOT_ACTIVE) {
							$usrAction = 'skip';
							$usrActionMsg = __('User is already disabled');
							$usr['action-desc'] = 'skip-disable';

							$this->_setUserStatus($erambaUserData['login'], 'remove-already-disabled', __(
								'User is not in the group and should be disabled, but its already disabled'
							));
						} else {
							$usrAction = 'disable';
							$usrActionMsg = __('User will be disabled');

							$this->_setUserStatus($erambaUserData['login'], 'remove-disable', __(
								'User is not in the group and will be disabled according to sync settings'
							));
						}
					} elseif (in_array(LdapSynchronization::NO_USER_ACTION_DELETE, $erambaUser['no_user_actions'])) {
						$usrAction = 'delete';
						$usrActionMsg = __('User will be deleted');

						$this->_setUserStatus($erambaUserData['login'], 'remove-delete', __(
							'User is not in the group and will be removed according to sync settings'
						));
					}
					$usr['action'] = $usrAction;
					$usr['actionMsg'] = $usrActionMsg;
					$usr['data'] = [
						'id' => $erambaUserData['id'],
						'status' => $erambaUserData['status'],
					];

					if (!in_array($erambaUserData['login'], $users)) {
						$tmpLdap = array_flip($erambaUser['ldap_synchronizations']);
						unset($tmpLdap[$id]);

						$usr['data']['LdapSynchronization'] = array_flip($tmpLdap);
					}

					$usersResults[] = $usr;
				} else {
					if (!in_array($erambaUserData['login'], $users)) {
						$usr['action'] = 'skip';
						$usr['action-desc'] = 'skip-ignore';
						$usr['actionMsg'] = __('User will be ignored');
						$usr['data'] = [
							'id' => $erambaUserData['id']
						];
						$usr['login'] = $erambaUserData['login'];

						$tmpLdap = array_flip($erambaUser['ldap_synchronizations']);
						unset($tmpLdap[$id]);

						$usr['data']['LdapSynchronization'] = array_flip($tmpLdap);

						$usersResults[] = $usr;
					}

					
				}
			}
			//

			//
			// Find users which we need to add to eramba or skip because they already exists
			$erambaUsersLogins = array_keys($erambaUsers);
			foreach ($usersData as $userData) {
				$usr = [
					'login' => $userData['login']
				];

				if (!array_key_exists($userData['login'], $erambaUsers)) {
					// Get synchronizations names
					$LdapSynchronizationModel = ClassRegistry::init('LdapSynchronization');
					$ldapSyncsData = $LdapSynchronizationModel->find('all', [
						'conditions' => [
							'LdapSynchronization.id' => $userData['LdapSynchronization']
						]
					]);
					$ldapSyncsNames = Hash::extract($ldapSyncsData, '{n}.LdapSynchronization.name');

					$usr['data'] = $userData;
					$usr['additional_data'] = [
						'portals' => Hash::extract($portals, "{n}.name"),
						'groups' => Hash::extract($groups, "{n}.name"),
						'ldap_synchronizations' => $ldapSyncsNames
					];
					$usr['action'] = 'add';
					$usr['actionMsg'] = __('User will be added');

					$this->_setUserStatus($userData['login'], 'add', __(
						'User will be added'
					), $usr['data'], $usr['additional_data']);
				} else {
					$erambaUser = $erambaUsers[$userData['login']];
					$erambaUserData = $erambaUser['User'];

					// Skip admin and all local and previously manually added ldap users
					if ($erambaUserData['id'] == ADMIN_ID
						|| $erambaUserData['local_account'] == User::LOCAL_ACCOUNT
						|| empty($erambaUserData['ldap_sync'])
					) {
						if ($erambaUserData['local_account'] == User::LOCAL_ACCOUNT) {
							$this->_setUserStatus($erambaUserData['login'], 'local', __(
								'Already exists locally'
							));
						}

						continue;
					}

					/*
					Check if eramba user has portals which are not in any of his synchronization (then remove that portals)
					Check if eramba user does not have portals which are in current synchronization (then add that portals)
					--- same with groups ---
					
					Enable user if he is in disabled state
					
					action will be "skip" if we did nothing with user
					action will be "update" if we update any of user's information
					 */

					//
					// Update eramba user's portals
					$portalsEdited = false;
					$newPortals = [];
					// Remove portals which are not in any sync where user belongs
					foreach ($erambaUser['portals'] as $portalId) {
						if (in_array($portalId, $allSyncUserPortals[$userData['login']])) {
							$newPortals[] = $portalId;
						} else {
							$portalsEdited = true;
						}
					}
					// Add new portals from current sync
					foreach ($userData['Portal'] as $portalId) {
						if (!in_array($portalId, $erambaUser['portals'])) {
							$newPortals[] = $portalId;
							$portalsEdited = true;
						}
					}
					//
					
					//
					// Update eramba user's groups
					$newGroups = [];
					$groupsEdited = false;
					// Remove groups which are not in any sync where user belongs
					foreach ($erambaUser['groups'] as $groupId) {
						if (in_array($groupId, $allSyncUserGroups[$userData['login']])) {
							$newGroups[] = $groupId;
						} else {
							$groupsEdited = true;
						}
					}
					// Add new groups from current sync
					foreach ($userData['Group'] as $groupId) {
						if (!in_array($groupId, $erambaUser['groups'])) {
							$newGroups[] = $groupId;
							$groupsEdited = true;
						}
					}
					//
					
					//
					// Check if user has this ldap_synchronization attached
					$newLdapSyncs = $erambaUser['ldap_synchronizations'];
					$ldapSyncEdited = false;
					if (!in_array($data['id'], $erambaUser['ldap_synchronizations'])) {
						$newLdapSyncs[] = $data['id'];
						$ldapSyncEdited = true;
					}
					// 
					
					//
					// Update eramba user's status
					$statusEdited = false;
					$newStatus = null;
					if ($erambaUserData['status'] == User::STATUS_NOT_ACTIVE) {
						$statusEdited = true;
						$newStatus = User::STATUS_ACTIVE;
					}
					//

					if ($portalsEdited || $groupsEdited || $ldapSyncEdited || $statusEdited) {
						$usr['data'] = [
							'id' => $erambaUsers[$userData['login']]['User']['id']
						];
						$usr['additional_data'] = [];

						if ($portalsEdited) {
							// Get portals names
							$PortalModel = ClassRegistry::init('Portal');
							$portalsData = $PortalModel->find('all', [
								'conditions' => [
									'Portal.id' => $newPortals
								]
							]);
							$portalsNames = Hash::extract($portalsData, '{n}.Portal.name');

							$usr['data']['Portal'] = $newPortals;
							$usr['additional_data']['portals'] = $portalsNames;
						}

						if ($groupsEdited) {
							// Get groups names
							$GroupModel = ClassRegistry::init('Group');
							$groupsData = $GroupModel->find('all', [
								'conditions' => [
									'Group.id' => $newGroups
								]
							]);
							$groupsNames = Hash::extract($groupsData, '{n}.Group.name');

							$usr['data']['Group'] = $newGroups;
							$usr['additional_data']['groups'] = $groupsNames;
						}

						if ($ldapSyncEdited) {
							// Get synchronizations names
							$LdapSynchronizationModel = ClassRegistry::init('LdapSynchronization');
							$ldapSyncsData = $LdapSynchronizationModel->find('all', [
								'conditions' => [
									'LdapSynchronization.id' => $newLdapSyncs
								]
							]);
							$ldapSyncsNames = Hash::extract($ldapSyncsData, '{n}.LdapSynchronization.name');

							$usr['data']['LdapSynchronization'] = $newLdapSyncs;
							$usr['additional_data']['ldap_synchronizations'] = $ldapSyncsNames;
						}

						if ($statusEdited && $newStatus !== null) {
							$usr['data']['status'] = $newStatus;
						}

						$usr['action'] = 'update';
						$usr['actionMsg'] = __('User will be updated');

						$this->_setUserStatus($erambaUserData['login'], 'update', __(
							'User will be updated'
						), $usr['data'], $usr['additional_data']);
					} else {
						$usr['action'] = 'skip';
						$usr['actionMsg'] = __('User already exists and is up to date');
						$usr['action-desc'] = 'skip-add';

						$this->_setUserStatus($erambaUserData['login'], 'skip-no-change', __(
							'User already exists and is up to date'
						));
					}
				}

				$usersResults[] = $usr;
			}
			//
		}

		if ($saveToDb && !empty($data)) {
			// Audit trait - sync process started
			$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_SYNC_STARTED)
				->foreignKey($data['id'])
				->message([$data['name']])
				->log();
			
			if ($ldapGroupExists) {
				$syncResult = self::SYNC_RESULT_SUCCESS;
				$syncResultMsg = __('Successfully synchronized');

				$processedUsersCount = 0;
				foreach ($usersResults as $key => $user) {
					if (!empty($limit) && $processedUsersCount >= $limit) {
						break;
					}

					$this->User->clear();
					if ($user['action'] == 'skip') {
						if ($user['action-desc'] == 'skip-add') {
							// Audit trail - account found in LDAP group
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_FOUND)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login'], $data['ldap_group']])
								->log();
							
							// Audit trail - account already exists in eramba we are not creating it again
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_EXISTS)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login']])
								->log();
						} elseif ($user['action-desc'] == 'skip-disable') {
							// Audit trail - account exists in eramba but does not exists in LDAP group
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_NOT_FOUND)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login'], $data['ldap_group'], __('disabling')])
								->log();

							// Audit trail - account is already disabled - no action required
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_ALREADY_DISABLED)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login']])
								->log();
						} elseif ($user['action-desc'] == 'skip-ignore') {
							// Audit trail - account exists in eramba but does not exists in LDAP group - account will be ignored
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login'], $data['ldap_group']])
								->log();
						} elseif ($user['action-desc'] == 'skip-ignore-local') {
							// Audit trail - account exists in eramba as local and also exists in LDAP group - account will be ignored
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_IGNORED_LOCAL)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login'], $data['ldap_group']])
								->log();
						}

						if (isset($user['data']['LdapSynchronization'])) {
							$ud = [
								'User' => [
									'id' => $user['data']['id'],
									'LdapSynchronization' => $user['data']['LdapSynchronization']
								]
							];
							$v = (bool) $this->User->validateAssociated($ud, [
								'atomic' => true,
								'deep' => true,
								'fieldList' => ['LdapSynchronization']
							]);

							if ($v) {
								$this->User->saveAssociated($ud, [
									'atomic' => true,
									'validate' => false,
									'deep' => true,
									'callbacks' => false,
									'fieldList' => ['LdapSynchronization']
								]);
							}
						}

					} elseif ($user['action'] == 'add') {
						// Audit trail - account found in LDAP group
						$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_FOUND)
							->foreignKey($data['id'])
							->message([$data['name'], $user['login'], $data['ldap_group']])
							->log();

						//
						// Validate user and save validation errors
						$ud = [
							'User' => $user['data']
						];
						$userValid = $this->User->validateAssociated($ud, [
							'atomic' => true,
							'deep' => true
						]);
						$usersResults[$key]['valid'] = $userValid;
						$usersResults[$key]['validationErrors'] = $this->User->validationErrors;
						//
						
						$res = false;
						if ($userValid == true) {
							// Audit trail - account does not exists in eramba - validated successfully
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_SUCCESS)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login']])
								->log();

							$res = $this->User->saveAssociated($ud, [
								'validate' => false,
								'atomic' => true,
								'deep' => true
							]);

							if ($res) {
								// Audit trail - account has been created successfully
								$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_SUCCESS)
									->foreignKey($data['id'])
									->message([$data['name'], $user['login']])
									->log();
							} else {
								// Audit trail - account creating failed
								$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_CREATE_FAIL)
									->foreignKey($data['id'])
									->message([$data['name'], $user['login']])
									->log();
							}
						} else {
							// Audit trail - account cannot be created because of validation errors
							$validationErrorsStr = implode(", ", Hash::extract($this->User->validationErrors, "{s}.0"));
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_VALIDATION_FAIL)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login'], $validationErrorsStr])
								->log();

							$this->_setUserStatus($user['data']['login'], 'add-validation-error', __(
								'User cannot be created because of validation error (%s);', $validationErrorsStr
							));
						}

						$usersResults[$key]['actionResult'] = $res;

						++$processedUsersCount;
					} elseif ($user['action'] == 'update') {
						// Audit trail - account exists in eramba and ldap server
						$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_FOUND)
							->foreignKey($data['id'])
							->message([$data['name'], $user['login'], $data['ldap_group']])
							->log();

						$fieldList = [];
						foreach ($user['data'] as $ud_key => $ud_val) {
							if ($ud_key !== 'id') {
								$fieldList[] = $ud_key;
							}
						}

						//
						// Validate user and save validation errors
						$ud = [
							'User' => $user['data']
						];
						$res = $this->User->validateAssociated($ud, [
							'atomic' => true,
							'deep' => true,
							'fieldList' => $fieldList
						]);
						$usersResults[$key]['valid'] = $res;
						$usersResults[$key]['validationErrors'] = $this->User->validationErrors;
						//

						if ((bool) $res) {
							$res &= $this->User->saveAssociated($ud, [
								'atomic' => true,
								'validate' => false,
								'deep' => true,
								'fieldList' => $fieldList,
								'callbacks' => false
							]);

							++$processedUsersCount;
						}

						$usersResults[$key]['actionResult'] = $res;

						if ($res) {
							// Audit trail - successfully updated
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_SUCCESS)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login']])
								->log();
						} else {
							// Audit trail - updating failed
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_UPDATE_FAIL)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login']])
								->log();
						}
					} elseif ($user['action'] == 'disable') {
						// Audit trail - account exists in eramba but does not exists in LDAP group
						$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_NOT_FOUND)
							->foreignKey($data['id'])
							->message([$data['name'], $user['login'], $data['ldap_group'], __('disabling')])
							->log();
						
						$res = true;
						if ($user['data']['status'] != User::STATUS_NOT_ACTIVE) {
							$ud = [
								'id' => $user['data']['id'],
								'status' => User::STATUS_NOT_ACTIVE
							];

							$fieldList = [
								'status'
							];

							if (isset($user['data']['LdapSynchronization'])) {
								$ud['LdapSynchronization'] = $user['data']['LdapSynchronization'];

								$fieldList[] = 'LdapSynchronization';
							}

							$ud = [
								'User' => $ud
							];
							$v = (bool) $this->User->validateAssociated($ud, [
								'atomic' => true,
								'deep' => true,
								'fieldList' => $fieldList
							]);

							if ($v) {
								$res = $this->User->saveAssociated($ud, [
									'atomic' => true,
									'validate' => false,
									'callbacks' => false
								], $fieldList);

								++$processedUsersCount;
							}
						}

						$usersResults[$key]['actionResult'] = !empty($res) ? true : false;

						if (!empty($res)) {
							// Audit trail - successfully disabled
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_SUCCESS)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login'], $data['ldap_group']])
								->log();
						} else {
							// Audit trail - disabling failed
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_DISABLE_FAIL)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login']])
								->log();
						}
					} elseif ($user['action'] == 'delete') {
						// Audit trail - account exists in eramba but does not exist in LDAP group
						$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_ACCOUNT_NOT_FOUND)
							->foreignKey($data['id'])
							->message([$data['name'], $user['login'], $data['ldap_group'], __('deleting')])
							->log();
						
						$res = $this->User->delete($user['data']['id']);
						$usersResults[$key]['actionResult'] = $res;

						if ($res) {
							// Audit trail - successfully deleted
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_SUCCESS)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login'], $data['ldap_group']])
								->log();
						} else {
							// Audit trail - deleting failed
							$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_ERAMBA_ACCOUNT_DELETE_FAIL)
								->foreignKey($data['id'])
								->message([$data['name'], $user['login']])
								->log();
						}

						++$processedUsersCount;
					}
				}
			} else {
				// Audit trail - sync process terminated because ldap group no longer exists on LDAP server
				$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_SYNC_FAIL_GROUP_NOT_FOUND)
					->foreignKey($data['id'])
					->message([$data['name'], $data['ldap_group']])
					->log();

				$this->ldapSyncNotification($data['id'], 'failure');
			}

			// Audit trail - sync process finished
			$this->LdapSynchronization->createSystemLog(LdapSynchronization::SYSTEM_LOG_LDAP_SYNC_FINISHED)
				->foreignKey($data['id'])
				->message([$data['name']])
				->log();
		} else {
			if (!empty($data)) {
				if ($ldapGroupExists) {
					$syncResult = self::SYNC_RESULT_SUCCESS;
					$syncResultMsg = __('LDAP synchronized successfully');
				}
			}
		}

		if (!$ldapGroupExists) {
			$syncResult = self::SYNC_RESULT_FAILURE;
			$syncResultMsg = __('The LDAP Group configured for this Sync does no longer exist on the LDAP directory, we therefore can not use this sync anymore until the group is updated or re-created on the directory');
		}
		
		return [
			'syncResult' => $syncResult,
			'syncResultMsg' => $syncResultMsg,
			'results' => $usersResults
		];
	}

	/**
	 * Send notification about LDAP Sync success or failure
	 * @param  int    $ldapSynchronizationId Ldap Synchronization ID
	 * @param  string $type                  Options: success, failure
	 * @return
	 */
	protected function ldapSyncNotification($ldapSynchronizationId, $type = 'failure')
	{
		$notifType = "";
		if ($type == 'success') {
			$notifType = 'ldap_sync_success';
		} elseif ($type == 'failure') {
			$notifType = 'ldap_sync_failed';
		}

		if ($notifType != "") {
			// trigger notification
			$this->LdapSynchronization->triggerNotification(
				$notifType,
				$ldapSynchronizationId,
				[
					'force' => true
				]
			);
		}
	}

	public function testSync(array $data)
	{
		if (empty($data)) {
			return false;
		}

		$ldapAuthConnectorId = intval($data['ldap_auth_connector_id']);
		$ldapGroupConnectorId = intval($data['ldap_group_connector_id']);
		$ldapGroup = $data['ldap_group'];

		// Get list of users from all LDAP groups
		$users = $this->listLdapUsers($ldapGroupConnectorId, $ldapGroup);

		// Try to find any valid user in LDAP server
		$usersData = $this->getLdapUsersData($ldapAuthConnectorId, $users, true);

		if (!empty($usersData)) {
			return true;
		} else {
			return false;
		}
	}

	protected function listLdapUsers($groupConnectorId, $groups)
	{
		$LdapGroupConnector = $this->getLdapGroupConnector($groupConnectorId);

		if (!is_array($groups)) {
			$groups = [$groups];
		}

		$ldapGroups = [];
		foreach ($groups as $group) {
			$ldapGroups[$group] = $group;
		}

		$members = [];
		if (!empty($LdapGroupConnector)) {
			$members = $LdapGroupConnector->getMemberList($ldapGroups);
		}
		
		return !empty($members) ? $members : [];
	}

	public function getLdapGroupsList($groupConnectorId)
	{
		$LdapGroupConnector = $this->getLdapGroupConnector($groupConnectorId);

		$groups = [];
		if (!empty($LdapGroupConnector)) {
			$groups = $LdapGroupConnector->getGroupList();
		}
		
		return !empty($groups) ? $groups : [];
	}

	protected function getLdapGroupConnector($groupConnectorId)
	{
		$ldapData = $this->LdapConnector->find('first', [
			'conditions' => [
				'LdapConnector.id' => $groupConnectorId,
				'LdapConnector.type' => 'group'
			],
			'recursive' => -1
		]);

		if (!empty($ldapData)) {
			$data = $ldapData['LdapConnector'];

			$LdapGroupConnector = $this->LdapConnector->getConnector($data);
			$ldapConnection = $LdapGroupConnector->connect();

			if ($ldapConnection) {
				return $LdapGroupConnector;
			}
		}

		return false;
	}

	/**
	 * Get data of users from LDAP server
	 * @param  int   $authConnectorId ID of LDAP authenticator connector
	 * @param  array $users           Array of users [user => user]
	 * @param  bool  $firstValid      Whether or not to get only first found valid user
	 * @return array                  Array of users with data retrieved from LDAP server
	 */
	protected function getLdapUsersData(int $authConnectorId, array $users, $firstValid = false)
	{
		$usersData = [];

		$ldapData = $this->LdapConnector->find('first', [
			'conditions' => [
				'LdapConnector.id' => $authConnectorId,
				'LdapConnector.type' => 'authenticator'
			],
			'recursive' => -1
		]);

		if (!empty($ldapData)) {
			$data = $ldapData['LdapConnector'];

			$LdapAuthConnector = $this->LdapConnector->getConnector($data);
			$ldapConnection = $LdapAuthConnector->connect();

			if ($ldapConnection) {
				$fields = [
					'login' => $data['ldap_auth_attribute'],
					'name' => $data['ldap_name_attribute'],
					'email' => $data['ldap_email_attribute']
				];
				foreach ($users as $user) {
					$userData = $LdapAuthConnector->getUser($user);
					if (isset($userData['count']) && $userData['count'] > 0) {
						$allClear = true;
						$userData = $userData[0];
						$ud = [];
						foreach ($fields as $key => $val) {
							$val = strtolower($val);
							if (isset($userData[$val]) && isset($userData[$val]['count']) && $userData[$val]['count'] > 0) {
								if ($key == 'name') {
									$fullName = explode(" ", $userData[$val][0]);
									if (count($fullName) > 1) {
										$ud['name'] = $fullName[0];
										$ud['surname'] = $fullName[1];
									} else {
										$allClear = false;
									}
								} else {
									$ud[$key] = $userData[$val][0];
								}
							} else {
								$allClear = false;
							}
						}

						$usersData[] = [
							'valid' => $allClear,
							'data' => $ud
						];

						if ($allClear && $firstValid) {
							break;			
						}
					}
				}
			}
		}

		if ($firstValid) {
			foreach ($usersData as $key => $userData) {
				if ($userData['valid'] == false) {
					unset($usersData[$key]);
				}
			}
		}
		
		return $usersData;
	}

	protected function _setUserStatus($login, $status, $message, $data = [], $additionalData = [])
	{
		$statusMap = array_flip([
			'local',
			'add-validation-error',
			'add',
			'update',
			'skip-no-change',
			'remove-ignore',
			'remove-disable',
			'remove-already-disabled',
			'remove-delete',
		]);

		if (!empty($this->_userStatuses[$login])
			&& $statusMap[$this->_userStatuses[$login]['status']] < $statusMap[$status]
		) {
			return;
		}

		if (isset($this->_userStatuses[$login]['status']['additional_data']['portals'])
			&& isset($additionalData['portals'])
		) {
			$additionalData['portals'] = array_unique(array_merge($this->_userStatuses[$login]['status']['additional_data']['portals'], $additionalData['portals']));
		}

		if (isset($this->_userStatuses[$login]['status']['additional_data']['groups'])
			&& isset($additionalData['groups'])
		) {
			$additionalData['groups'] = array_unique(array_merge($this->_userStatuses[$login]['status']['additional_data']['groups'], $additionalData['groups']));
		}

		$this->_userStatuses[$login] = [
			'login' => $login,
			'status' => $status,
			'message' => $message,
			'data' => $data,
			'additional_data' => $additionalData
		];
	}

	public function getUserStatuses()
	{
		return $this->_userStatuses;
	}
}
