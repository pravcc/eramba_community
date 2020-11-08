<?php
App::uses('AppController', 'Controller');
App::uses('Hash', 'Utility');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('FormReloadListener', 'Controller/Crud/Listener');
App::uses('User', 'Model');
App::uses('Portal', 'Model');
App::uses('NotificationSystemManager', 'NotificationSystem.Lib');
App::uses('NotificationSystemSubject', 'NotificationSystem.Lib');
App::uses('CakeText', 'Utility');
App::uses('Routing', 'Router');
App::uses('AppErrorHandler', 'Error');

class UsersController extends AppController
{
	public $name = 'Users';
	public $uses = array('User', 'Setting');
	public $helpers = ['ImportTool.ImportTool', 'Translations.Translations'];
	public $components = array('SamlAuth', 'OauthGoogleAuth', 'Ticketmaster', 'LdapConnectorsMgt', 'Paginator', 'Search.Prg',
		'BruteForce.BruteForce' => [
			'portal' => Portal::PORTAL_MAIN,
		],
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'profile' => [
					'className' => 'AppEdit',
					'redirect' => array(
						'post_edit' => array(
							'url' => array('action' => 'profile')
						)
					),
					'validateId' => false
				]
			],
			'listeners' => [
				'Widget.Widget', 'LdapSync.LdapSync', 'BulkActions.BulkActions', 'AdvancedFilters.AdvancedFiltersSetup', 'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem'
					]
				]
			]
		]
	);

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter()
	{
		if ($this->params['action'] == 'changeLanguage') {
			$this->Components->disable('Acl');
			$this->Components->disable('Auth');
		}

		$this->Crud->enable(['index', 'add', 'edit', 'delete']);

		$this->Auth->allow('resetpassword', 'useticket', 'logout', 'changeLanguage', 'changeDefaultPassword', 'prepareAccount');

		$disabledAuthorizeActions = [
			'changeDefaultPassword', 'prepareAccount', 'profile', 'searchLdapUsers', 'resetpassword', 'useticket', 'login',
			'logout', 'changeLanguage', 'unblock', 'checkConflicts'
		];

		if (in_array($this->request->params['action'], $disabledAuthorizeActions)) {
            $this->Auth->authorize = false;
        }

		parent::beforeFilter();

		$this->set('isUserAdmin', $this->isUserAdmin());
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		// $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('ImportTool', 'ImportTool.ImportTool');
		$this->Crud->addListener('SettingsSubSection', 'SettingsSubSection');

		$this->title = __('User Accounts');
		$this->subTitle = __('Manage your system user accounts. Account must exist for each individual that wants to use eramba indistinctly if LDAP is used or not.');

		return $this->Crud->execute();
	}

	public function add()
	{
		$this->title = __('Create a User Account');
		$this->subTitle = __('Create system users and assign them the appropiate Group Roles');

		$this->checkLdapConnection();

		$this->Crud->on('beforeHandle', function(CakeEvent $event)
		{
			// Fill form data with ldap user data if this is form reload request from field ldap_user
			$this->setDataFromLdapUser();
		});

		$this->Crud->on('beforeSave', function(CakeEvent $event)
		{
			if (!empty($event->subject->import)) {
				return;
			}

			if ((empty($this->ldapAuth['LdapConnectorAuthentication']['auth_saml']) &&
				empty($this->ldapAuth['LdapConnectorAuthentication']['oauth_google']) &&
				empty($this->ldapAuth['LdapConnectorAuthentication']['auth_users'])) ||
				!empty($this->request->data['User']['local_account'])) {
				$this->User->addPasswordRequiredValidationRule();
			}
		});

		$this->Crud->on('afterSave', array($this, '_afterCreated'));

		$this->Crud->on('beforeRender', function(CakeEvent $event)
		{
			$this->request->data['User']['pass'] = $this->request->data['User']['pass2'] = '';
		});

		return $this->Crud->execute();
	}

	public function _afterCreated(CakeEvent $event) {
		if ($event->subject->success) {
			// email notification
			$requestData = $event->subject->request->data;
			$username = $requestData['User']['login'];
			$password = (isset($requestData['User']['pass'])) ? $requestData['User']['pass'] : '';

			$model = $event->subject->model;
			$id = $event->subject->id;

			$conds = [
				'User.id' => $id,
				'User.local_account' => 1
			];

			$User = ClassRegistry::init('User');
			$count = $User->find('count', [
				'conditions' => $conds,
				'recursive' => -1
			]);

			if ($count) {
				// trigger notification
				$User->triggerNotification(
					'new_user',
					$id,
					[
						'force' => true,
						'username' => $username,
						'password' => $password,
						'main_portal_login_url' => Router::url(['plugin' => false, 'controller' => 'users', 'action' => 'login'], true),
						'online_assessment_portal_login_url' => Router::url(['plugin' => 'vendor_assessments', 'controller' => 'vendorAssessmentPortal', 'action' => 'login'], true),
						'account_review_portal_login_url' => Router::url(['plugin' => 'account_reviews', 'controller' => 'accountReviewPortal', 'action' => 'login'], true)
					]
				);
			}
		}
	}

	/**
	 * Checks if ldap connection can be made.
	 */
	private function checkLdapConnection()
	{
		if (empty($this->ldapAuth['LdapConnectorAuthentication']['auth_users'])) {
			return false;
		}
		
		$connector = $this->ldapAuth['AuthUsers'];

		$LdapConnector = $this->LdapConnectorsMgt->getConnector($connector);
		$ldapConnection = $LdapConnector->connect();
		$LdapConnector->unbind();

		$this->set('ldapConnection', $ldapConnection);
	}

	/**
	 * Search all existing users using LdapConnector.
	 */
	public function searchLdapUsers()
	{
		// Disable controller auto rendering layout and view
		$this->autoLayout = false;
		$this->autoRender = false;

		// Disable YoonityJS auto response
		$this->YoonityJSConnector->deny();

		if (empty($this->ldapAuth['LdapConnectorAuthentication']['auth_users'])) {
			return false;
		}
		
		$connector = $this->ldapAuth['AuthUsers'];

		$LdapConnector = $this->LdapConnectorsMgt->getConnector($connector);
		$ldapConnection = $LdapConnector->connect();

		// $ldapConnection = $this->LdapConnectorsMgt->ldapConnect($connector['host'], $connector['port'], $connector['ldap_bind_dn'], $connector['ldap_bind_pw']);
		$this->set('ldapConnection', $ldapConnection);
		
		if ($ldapConnection !== true) {
			echo json_encode(array('success' => false, 'message' => __('LDAP connection has failed.')));
			exit;
		}
		
		$_users = $LdapConnector->searchUsers($this->request->query['q']);
		// $_users = $this->LdapConnectorsMgt->searchUsers($connector, $this->request->query['q']);

		$users = array();
		foreach ($_users as $key => $value) {
			$users[] = array(
				'id' => $value,
				'text' => $value
			);
		}

		echo json_encode($users);
		exit;
	}

	public function edit($id = null)
	{
		$this->title = __('Edit User Accounts');
		$this->subTitle = __('Edit system users and assign them the appropiate Group Roles');

		$isLdapSyncUser = $this->User->find('count', [
			'conditions' => [
				'User.id' => $id,
				'User.ldap_sync' => 1
			],
			'recursive' => -1
		]);

		if ((!$this->isUserAdmin() && $id == ADMIN_ID) || !empty($isLdapSyncUser)) {
			throw new ForbiddenException(__('You are not allowed to edit this user.'));
		}

		$this->Crud->on('beforeSave', function(CakeEvent $event)
		{
			if (!$this->isUserAdmin()) {
				$user = $this->User->find('first', [
					'conditions' => [
						'User.id' => $event->subject->id
					],
					'fields' => [
						'User.local_account'
					],
					'recursive' => -1
				]);

				$this->request->data['User']['local_account'] = $user['User']['local_account'];
			}
		});

		$this->Crud->on('afterSave', array($this, '_afterEdited'));

		$this->Crud->on('beforeRender', function(CakeEvent $event)
		{
			$this->request->data['User']['old_pass'] = $this->request->data['User']['pass'] = $this->request->data['User']['pass2'] = '';
		});

		return $this->Crud->execute();
	}

	public function _afterEdited(CakeEvent $event)
	{
		$id = $event->subject->id;
		if ($id != $this->Auth->user('id') &&
			!empty($this->request->data['User']['pass'])) {
			$requestData = $event->subject->request->data;
			$username = $requestData['User']['login'];
			$password = $requestData['User']['pass'];

			// trigger notification
			ClassRegistry::init('User')->triggerNotification(
				'password_change',
				$id,
				[
					'force' => true,
					'username' => $username,
					'password' => $password,
				]
			);
		}
	}

	public function delete($id = null)
	{
		$this->title = __('User');
		$this->subTitle = __('Delete a User');

		$this->Crud->on('beforeDelete', function(CakeEvent $event)
		{
			if ($event->subject->id == ADMIN_ID) {
				throw new ForbiddenException(__('Admin cannot be deleted.'));
			}

			if ($event->subject->id == $this->logged['id']) {
				throw new ForbiddenException(__('You cannot delete yourself.'));
			}
		});

		return $this->Crud->execute();
	}

	public function profile()
	{
		//
		// Enable old password field
		$this->_FieldDataCollection->old_pass->config('editable', true);
		//
		
		//
		// Remove unused fields
		$fieldList = ['name', 'surname', 'old_pass', 'pass', 'pass2', 'email'];
		$collectionFields = array_keys($this->_FieldDataCollection->getList());
		foreach ($collectionFields as $fieldName) {
			if (!in_array($fieldName, $fieldList)) {
				$this->_FieldDataCollection->remove($fieldName);
			}
		}
		//
		
		if (!empty($this->request->data)) {
			if ($this->logged['local_account'] && !empty($this->request->data['User']['pass'])) {
				$this->User->addOldPassRequiredValidationRule();
			}
		}

		$this->Crud->on('beforeSave', function(CakeEvent $event)
		{
			$fieldList = ['password'];

			$this->Crud->action('profile')->config('saveOptions', [
				'fieldList' => array_merge(['old_pass', 'pass'], $fieldList)
			]);
		});

		$this->Crud->on('beforeFind', function(CakeEvent $event)
		{
			$event->subject->query['conditions'] = [
				'User.id' => $this->logged['id'],
				'User.status' => USER_ACTIVE
			];
		});

		$this->Crud->on('beforeRender', function(CakeEvent $event)
		{
			$this->request->data['User']['old_pass'] = $this->request->data['User']['pass'] = $this->request->data['User']['pass2'] = '';
		});

		$this->set('profile', true);

		return $this->Crud->execute(null, ['id' => $this->logged['id']]);
	}

	/**
	 * Bridge action that configures user account after first successful login.
	 * 
	 * @param  int $userId  User ID.
	 */
	public function prepareAccount($userId)
	{
		$redirect = (!empty($this->request->query['redirect'])) ? $this->request->query['redirect'] : ['controller' => 'users', 'action' => 'login'];

		if ($this->Auth->user('account_ready') == 1) {
			$this->redirect($redirect);
		}

		$this->title = __('Your account is getting configured');
		$this->layout = 'login';

		if ($this->request->is('post')) {

			try {
				$subject = $this->Crud->trigger('onFirstLogin', ['id' => $userId]);

				if ($subject->stopped) {
					$ret = false;
				} else {
					$ret = true;
				}
			} catch (Exception $e) {
				AppErrorHandler::logException($e);

				$ret = false;
			}

			// if account setup successfully
			if ($ret) {
				$userData['User']['account_ready'] = 1;

				$this->User->set($userData);
				$this->User->id = $userId;
				$ret = $this->User->save(null, [
					'validate' => true,
					'fieldList' => ['account_ready'],
				]);

				// write updated value into these auth sessions
				$writeToAuths = [
					'User',
					'AccountReview',
					'VendorAssessment'
				];

				if ($ret) {
					$auths = array_keys($this->Session->read('Auth'));
					foreach ($auths as $auth) {
						if (in_array($auth, $writeToAuths)) {
							$this->Session->write('Auth.' . $auth . '.account_ready', 1);
						}
					}

					$this->redirect($redirect);
				}
			} else {
				$this->Session->setFlash(__('Error occured while trying to setup your account. Please try again'), FLASH_ERROR);
				$this->logout();
			}
		}
		
		$this->set('prepareAccountUserId', $userId);
		$this->set('prepareAccountRedirect', $redirect);
	}

	public function changeDefaultPassword($userId)
	{
		$redirect = (!empty($this->request->query['redirect'])) ? $this->request->query['redirect'] : ['controller' => 'users', 'action' => 'login'];

		if (!$this->Auth->isUserLoggedInOnAnyPortal($userId)) {
			$this->Flash->default(__('Password of this user cannot be changed.'));
			$this->redirect($redirect);
		}

		//
		// Check if user need to use this function and if he don't, redirect him to login page
		if (!$this->Auth->isPasswordChangeRequired($userId)) {
			$this->Flash->default(__('You already changed your default password. If you want to change your password again, go to your profile and change it there.'));
			$this->redirect($redirect);
		}
		//
		
		$this->title = __('Password change');
		$this->layout = 'login';

		if ($this->request->is('post')) {
			$this->User->addOldPassRequiredValidationRule();
			$this->User->addPasswordRequiredValidationRule();

			$userData = $this->request->data;

			// Set default password for this user as changed
			$userData['User']['default_password'] = 0;

			$fieldList = ['old_pass', 'pass', 'password', 'default_password'];
			if ($userId == ADMIN_ID) {
				$fieldList[] = 'email';
			}

			$this->User->set($userData);
			$this->User->id = $userId;
			if ($this->User->save(null, [
					'validate' => true,
					'fieldList' => $fieldList,
					'autoSetDefaultPasswordState' => false,
					'user_id' => $userId
				])) {

				// Save success result to session
				$this->Auth->setDefaultPasswordChange($userId, 0);

				$this->Flash->success(__('Your password was successfully changed'));
				$this->redirect($redirect);
			} else {
				$this->Flash->error(__('Something went wrong, please try again'));
			}
		}

		$this->request->data['User']['old_pass'] = $this->request->data['User']['pass'] = $this->request->data['User']['pass2'] = '';

		$this->set('changePassUserId', $userId);
		$this->set('changePassRedirect', $redirect);
	}

	/**
	  * Method send email with link for password change.
	  */
	public function resetpassword() {
		$this->title = __('Did you forget your password?');
		$this->layout = 'login';

		if (!empty($this->request->data)) {
			$this->request->data['User']['email'] = Purifier::clean($this->request->data['User']['email'], 'Strict');
			
			//find user
			$user = $this->User->find('first', array (
				'conditions' => array(
					'User.email'  => $this->request->data['User']['email'],
					'User.status' => USER_ACTIVE
				),
				'fields' => array('User.email', 'User.local_account'),
				'recursive' => -1
			));

			$this->Flash->success(__('We will process your request soon - if the email you provided is valid you will receive an email with instructions on how to reset your password'));

			if (empty($user)) {
				return false;
			} else if ($user['User']['local_account'] == 0) {
				return false;
			}

			$this->loadModel('Ticket');

			//create hash
			$ticketHash = $this->Ticketmaster->createHash();

			//data for email
			$emailData = array(
				'token' => $ticketHash,
				'emailTitle' => __('Reset your password'),
				'redirect' => (!empty($this->request->query['redirect'])) ? $this->request->query['redirect'] : null
			);
			$emailResult = false;

			//data for ticket
			$data = array();
			$data['Ticket']['hash'] = $ticketHash;
			$data['Ticket']['data'] = $user['User']['email'];
			$data['Ticket']['expires'] = $this->Ticketmaster->getExpirationDate();

			//save ticket
			if (($ticketResult = $this->Ticket->save($data))) {
				//send the email with ticket
				$emailInstance = new ErambaCakeEmail('default');
				$emailInstance->to($user['User']['email']);
				$emailInstance->subject(__('Reset Your Password'));
				$emailInstance->template('reset_password');
				$emailInstance->viewVars($emailData);
				$emailInstance->instant(true);
				
				$emailResult = $emailInstance->send();
			}

			$this->request->data = null;
		}
	}

	/**
	  * Check ticket and use it if valid
	  *
	  * @param string $hash
	  */
	public function useticket($hash = null) {
		$this->title = __('Password change');
		$this->layout = 'login';
		$this->loadModel('Ticket');

		$redirect = array('plugin' => false, 'controller' => 'users', 'action' => 'login');
		if (!empty($this->request->query['redirect'])) {
			$redirect = $this->request->query['redirect'];
		}

		if ($this->request->is('post')) {
			$this->User->addPasswordRequiredValidationRule();

			$this->User->set($this->request->data);
			if ($this->User->validates(array('fieldList' => array('pass')))) {

				//najdeme ticket pre dany hash
				$ticket = $this->Ticketmaster->checkTicket($this->request->data['User']['hash']);

				//ak sme nasli ticket
				if (!empty($ticket)) {
					//najdeme usera s danym emailom
					$user = $this->User->find('first', array(
						'conditions' => array(
							'User.email'  => $ticket['Ticket']['data'],
							'User.status' => USER_ACTIVE
						),
						'recursive' => -1
					));

					//ak taky user existuje
					if (!empty($user)) {

						$this->User->id = $user['User']['id'];
						// $ret = (bool) $this->User->saveField('password', Security::hash($this->request->data['User']['pass']));
						$ret = $this->User->save(null, true, array('password'));

						//ulozime uzivatela s novym heslom
						if ($ret) {
							//oznavime ticket ako pouzity, aby sa uz nedal pouzit znovu
							$this->Ticketmaster->useTicket($this->request->data['User']['hash']);

							$this->Session->setFlash(__('Your password was successfully changed. Now you can login again.'), FLASH_OK);
							$this->redirect($redirect);
						}
						else {
							$this->Session->setFlash(__('Error happened while processing your request. Please try again.'), FLASH_ERROR);
						}
					}
					else {
						$this->Session->setFlash(__('Requested ticket is invalid. Please contact the support center.'), FLASH_ERROR);
						$this->redirect($redirect);
					}
				}
				else{
					$this->Session->setFlash(__('Requested ticket is invalid. Please try the password recovery process again.'), FLASH_ERROR);
					$this->redirect($redirect);
				}
			}
			$this->request->data['User']['pass'] = $this->request->data['User']['pass2'] = '';
		}
		else {
		//skontrolujeme ci ticket existuje
			$ticket = $this->Ticketmaster->checkTicket($hash);

			if (!empty($ticket)) {
				//najdeme uzivatela s danym emailom
				$user = $this->User->find('first', array(
					'conditions' => array(
						'User.email'  => $ticket['Ticket']['data'],
						'User.status' => USER_ACTIVE
					),
					'fields' => array('User.email'),
					'recursive' => -1
				));

				if (!empty($user)) {
					$this->request->data['User']['hash'] = $hash;
				}
				else {
					$this->Session->setFlash(__('Requested ticket is invalid. Please try the password recovery process again.'), FLASH_ERROR);
					$this->redirect($redirect);
				}
			}
			else {
				$this->Session->setFlash(__('Requested ticket is invalid. Please try the password recovery process again.'), FLASH_ERROR);
				$this->redirect($redirect);
			}
		}
	}

	public function login()
	{
		$this->title = __('Login');
		$this->layout = 'login';

		// Whether or not to show "Forgot password" button
		$showForgotPasswordBtn = false;

		if ($this->logged != null) {
			$this->redirect($this->Auth->loginRedirect);
		}

		if ($this->request->is('post') || isset($this->request->query['authuser'])) {
			// $this->request->data['User']['login'] = Purifier::clean($this->request->data['User']['login'], 'Strict');
			// $this->request->data['User']['password'] = Purifier::clean($this->request->data['User']['password'], 'Strict');
			
			if ($this->BruteForce->check()) {
				if ($this->Auth->login()) {
					$userId = $this->Auth->user('id');

					$this->User->createSystemLog(User::SYSTEM_LOG_LOGIN_SUCCESS)
						->result($this->Auth->user('login'))
						->subSubject(ClassRegistry::init('Portal'), Portal::PORTAL_MAIN)
						->message([$this->Auth->user('login')])
						->log();

					$dataSource = $this->Setting->getDataSource();
					$dataSource->begin();

					if ($this->sendLoginRequest()) {
						$dataSource->commit();
					}
					else {
						$dataSource->rollback();

						$this->Session->setFlash(__('Internet connection is not available. Please try again.'), FLASH_ERROR);
						$this->logout();
						exit;
					}

					$this->Session->write('UserLogged', true);

					// if default language could not be saved automatically after successful login
					if (!$this->saveChosenLanguage()) {
						// $this->Session->setFlash(__('Problem occured while saving default language to your account. Plaease, try again in your Profile page.'), FLASH_ERROR);
					}
					
					return $this->redirect($this->Auth->redirect());
				}
				else {
					$logLogin = (isset($this->request->data['User']['login'])) ? $this->request->data['User']['login'] : User::UNKNOWN_LOGIN;

					$this->User->createSystemLog(User::SYSTEM_LOG_LOGIN_FAIL)
						->result($logLogin)
						->subSubject(ClassRegistry::init('Portal'), Portal::PORTAL_MAIN)
						->message([$logLogin])
						->userId($this->User->getIdByLogin($logLogin))
						->log();

					if ($this->Auth->portalAllowed()) {
						// we put different warning for ldap login issue
						$login = $this->getLoginFormData('login');
						$notLocalUser = $this->User->find('count', array(
							'conditions' => array(
								'User.login' => $login,
								'User.local_account' => 0
							),
							'recursive' => -1
						));

						$ldapErr = $this->getLdapLoginError();

						// if dealing with user account that is NOT created locally while non-local authentication is disabled
						if (!empty($notLocalUser) &&
							empty($this->ldapAuth['LdapConnectorAuthentication']['auth_users']) &&
							empty($this->ldapAuth['LdapConnectorAuthentication']['oauth_google']) &&
							empty($this->ldapAuth['LdapConnectorAuthentication']['auth_saml'])) {
							$errorMsg = __('Your account is configured to be authenticated using LDAP, OAuth or SAML which is disabled at the moment.');
						} elseif (!empty($notLocalUser) && !empty($ldapErr)) { // trying to login a non local user when remote server configuration fails
							$errorMsg = $ldapErr;
						} elseif (!empty($notLocalUser) && !empty($this->ldapAuth['LdapConnectorAuthentication']['oauth_google'])) {
							$errorMsg = __('Your account is configured to be authenticated using OAuth. Only way how you can login is via "Sign in with Google" button.');
						} elseif (!empty($notLocalUser) && !empty($this->ldapAuth['LdapConnectorAuthentication']['auth_saml'])) {
							$errorMsg = __('Your account is configured to be authenticated using SAML. Only way how you can login is via "SAML authentication" button.');
						} else { // other cases of failure
							$customLdapError = false;
							if ($this->Auth->getAuthenticateObject('LDAPAuthenticate') instanceof LDAPAuthenticate) {
								$customLdapError = $this->Auth->getAuthenticateObject('LDAPAuthenticate')->getCustomError();
							}

							if ($customLdapError !== false) {
								$errorMsg = $customLdapError;
							} else {
								$errorMsg = __('The system was unable to log you in. Check that your username and password are typed correctly.');
							}

							// Show "Forgot password" button
							$showForgotPasswordBtn = true;
						}

						$this->Session->setFlash($errorMsg, FLASH_ERROR);
					}
				}
			}
			else {
				$this->Session->setFlash($this->BruteForce->getMessage(), FLASH_ERROR);
			}
		}

		$this->Translations->setAvailableTranslations();

		$this->set('showForgotPasswordBtn', $showForgotPasswordBtn);
		$this->set("oauthGoogleAllowed", $this->OauthGoogleAuth->isOauthGoogleAllowed());
		$this->set("oauthGoogleAuthUrl", $this->OauthGoogleAuth->getSanitizedAuthUrl());
		$this->set("samlAuthAllowed", $this->SamlAuth->isSamlAuthAllowed());
		$this->set("samlAuthUrl", $this->SamlAuth->getSamlAuthUrl());
	}

	private function getLoginFormData($which)
	{
		$data = null;
		$loginFormDataKeys = array('login', 'password', 'language');
		if (in_array($which, $loginFormDataKeys)) {
			$data = isset($this->request->data['User'][$which]) ? $this->request->data['User'][$which] : null;
		}

		return $data;
	}

	/**
	 * Saves language chosen on the login page.
	 *
	 * @return bool
	 */
	private function saveChosenLanguage() {
		$ret = true;

		// $lang = $this->getLoginFormData('language');
		// $login = $this->getLoginFormData('login');
		// if (langExists($lang)) {
		// 	$user = $this->User->find('first', array(
		// 		'conditions' => array(
		// 			'User.login' => $login
		// 		),
		// 		'fields' => array('id', 'language'),
		// 		'recursive' => -1
		// 	));

		// 	if ($user['User']['language'] != $lang) {
		// 		$this->User->id = $user['User']['id'];
		// 		$ret &= $this->User->saveField('language', $lang);
		// 		$ret &= $this->changeLanguageSession($lang);
		// 	}
		// }

		return $ret;
	}

	/**
	 * Sends a request about a user login with app ID information.
	 */
	private function sendLoginRequest() {
		$clientId = CLIENT_ID;

		if (empty($clientId)) {
			$clientId = $this->getClientId();
			if (empty($clientId)) {
				return false;
			}

			$ret = true;
			if (STATS_REQUEST && !empty($clientId) && !Configure::read('Eramba.offline')) {
				$this->ErambaHttp = $this->Components->load('ErambaHttp');
				$ret &= $this->ErambaHttp->registerClientID($clientId);
			}

			$this->genDefCronSecKey();
			$this->_setupCronUrl();

			return $ret;
		}

		return true;
	}

	/**
	 * Creates and returns client ID.
	 */
	private function getClientId() {
		$clientId = CLIENT_ID;

		if (empty($clientId)) {
			$clientId = Security::hash(microtime(true) . Configure::read('Eramba.version') . mt_rand(1, 999), 'sha1');
			if (!$this->Setting->updateVariable('CLIENT_ID', $clientId)) {
				return false;
			}
		}

		return $clientId;
	}

	/**
	 * Generate default crontab security key
	 * @return boolean
	 */
	private function genDefCronSecKey()
	{
		$key = $this->genRandChars(8);
		if (!$this->Setting->updateVariable('CRON_SECURITY_KEY', $key)) {
			return false;
		}

		return true;
	}

	/**
	 * Generate string with random alphanumeric characters
	 * @param  int|integer $limit How many character should string has
	 * @return string             New generated string
	 */
	private function genRandChars(int $limit = 8)
	{
		$key = "";
		$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
		for ($i = 0; $i < $limit; $i++) {
			$index = mt_rand(0, strlen($characters) - 1);
			$key .= $characters[$index];
		}

		return $key;
	}

	/**
	 * Setup a default CRON URL for CLI during first login.
	 * @return boolean
	 */
	protected function _setupCronUrl()
	{
		$url = Configure::read('App.fullBaseUrl');
		if (!$this->Setting->updateVariable('CRON_URL', $url)) {
			return false;
		}

		return true;
	}

	private function checkBruteForce() {
		$login = $this->request->data['User']['login'];

		$this->User->bindModel( array(
			'hasMany' => array(
				'SystemRecord' => array(
					'foreignKey' => 'foreign_key',
					'conditions' => array(
						'SystemRecord.model' => 'User'
					)
				),
				'UserBan'
			)
		) );
		$this->User->Behaviors->attach('Containable');

		$fromTime = CakeTime::format( 'Y-m-d H:i:s', CakeTime::fromString( '-' . BRUTEFORCE_SECONDS_AGO . ' seconds' ) );
		$user = $this->User->find( 'first', array(
			'fields' => array('id', 'blocked'),
			'conditions' => array(
				'User.login' => $login
			),
			'contain' => array(
				'SystemRecord' => array(
					'conditions' => array(
						'SystemRecord.created >' => $fromTime,
						'SystemRecord.type' => 5
					)
				),
				'UserBan' => array(
					'conditions' => array(
						'UserBan.until >' => CakeTime::format( 'Y-m-d H:i:s', CakeTime::fromString( 'now' ) )
					)
				)
			),
		) );

		if ( empty( $user ) ) {
			return true;
		}

		if ( ! empty( $user['UserBan'] ) ) {
			$this->Flash->error(__('This account has had too many wrong login attempts, we have blocked further logins for at most %s minutes.', date('i', (strtotime($user['UserBan'][0]['until']) - time()) + 60)));
			$this->redirect( array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => null) );
			exit;
		}

		if ( count( $user['SystemRecord'] ) >= BRUTEFORCE_WRONG_LOGINS ) {
			$this->loadModel( 'UserBan' );
			$until = CakeTime::format( 'Y-m-d H:i:s', CakeTime::fromString( '+' . BRUTEFORCE_BAN_FOR_MINUTES . ' minutes' ) );
			$this->UserBan->set( array(
				'user_id' => $user['User']['id'],
				'until' => $until
			) );
			$this->UserBan->save();

			$this->User->saveBlockedField($user['User']['id'], '1');

			$this->Session->setFlash(__('You are not allowed to login because you tried it too many times.'), FLASH_ERROR);
			$this->redirect( array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => null) );
			exit;
		}

		if ($user['User']['blocked']) {
			$this->User->saveBlockedField($user['User']['id'], '0');
		}
	}

	private function bruteForceRedirect() {
		$this->Session->setFlash(__('You are not allowed to login because you tried it too many times.'), FLASH_ERROR);
		$this->redirect( array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => null) );
		exit;
	}

	public function logout()
	{	
		$this->OauthGoogleAuth->logout();
		$this->redirect($this->Auth->logout());
	}

	/**
	 * Load LDAP user data while adding new user.
	 */
	protected function setDataFromLdapUser()
	{
		$ldapUser = isset($this->request->data['User']['ldap_user']) ? $this->request->data['User']['ldap_user'] : false;

		if ($ldapUser === false || !FormReloadListener::isFormReload('ldap_user')) {
			return false;
		}

		if (empty($this->ldapAuth['LdapConnectorAuthentication']['auth_users'])) {
			return false;
		}
		
		$connector = $this->ldapAuth['AuthUsers'];
		$connector['_ldap_auth_filter_username_value'] = $ldapUser;

		$ldapConnection = $this->LdapConnectorsMgt->ldapConnect($connector['host'], $connector['port'], $connector['ldap_bind_dn'], $connector['ldap_bind_pw']);
		$this->set('ldapConnection', $ldapConnection);
		
		if ($ldapConnection !== true) {
			return false;
		}
		
		$user = array_values($this->LdapConnectorsMgt->getData($connector));

		if (isset($user[0]) && !empty($user[0])) {
			if (!empty($connector['ldap_auth_attribute']) && isset($user[0][$connector['ldap_auth_attribute']])) {
				$this->request->data['User']['login'] = $user[0][$connector['ldap_auth_attribute']];
			}

			if (!empty($connector['ldap_name_attribute']) && isset($user[0][$connector['ldap_name_attribute']])) {
				$ldapName = trim($user[0][$connector['ldap_name_attribute']]);

				// split the ldap name in case there is a blank space and fill in surname field as well
				if (strpos($ldapName, ' ') !== false) {
					$explode = explode(' ', $ldapName, 2);
					$ldapName = $explode[0];
					$this->request->data['User']['surname'] = $explode[1];

				}
				$this->request->data['User']['name'] = $ldapName;
			}

			if (!empty($connector['ldap_email_attribute']) && isset($user[0][$connector['ldap_email_attribute']])) {
				$this->request->data['User']['email'] = $user[0][$connector['ldap_email_attribute']];
			}
			elseif (isset($connector['domain']) && !empty($connector['domain']) && !empty($this->request->data['User']['login'])) {
				$this->request->data['User']['email'] = $this->request->data['User']['login'].'@'.$connector['domain'];
			}
		}

		$this->request->data['User']['local_account'] = 0;
	}

	/**
	 * Wrapper function to cahnge language.
	 */
	public function changeLanguage($translationId)
	{
		$Translation = ClassRegistry::init('Traslations.Translation');

		if ($Translation->isTranslationAvailable($translationId)) {
			$this->Translations->writeTranslationToCookies($translationId);
		}

		return $this->redirect($this->referer());
	}

	/**
	 * Unblock user that has a brute force ban.
	 */
	public function unblock($userId) {
		$userId = Purifier::clean($userId, 'Strict');

		$user = $this->User->find('count', array(
			'conditions' => array(
				'User.id' => $userId
			),
			'recursive' => -1
		) );

		if (!empty($user)) {
			$dataSource = $this->User->getDataSource();
			$dataSource->begin();

			$ret = $this->User->unblockBan($userId);
			if ($ret) {
				$dataSource->commit();
				$this->Session->setFlash(__('User was successfully unlocked.'), FLASH_OK);
			}
			else {
				$dataSource->rollback();
				$this->Session->setFlash(__('Error occured while trying to unlock the user. Please try again.'), FLASH_ERROR);
			}
		}
		else {
			$this->Session->setFlash(__('User not found. Please try again.'), FLASH_ERROR);
		}

		$this->redirect(array('controller' => 'users', 'action' => 'index', 'admin' => false, 'plugin' => null));
	}

	/**
	 * Find out if a given user is ADMIN or if he has assigned an ADMIN group
	 * @return boolean
	 */
	protected function isUserAdmin()
	{
		if ($this->logged === null) {
			return false;
		}

		return $this->logged['id'] == ADMIN_ID || (isset($this->logged['Groups']) && is_array($this->logged['Groups']) && in_array(ADMIN_GROUP_ID, $this->logged['Groups']));
	}

	/**
	 * Checks conflicts in selected groups and renders informational box if there is any problem
	 * 
	 * @return void
	 */
	public function checkConflicts()
	{
		// Disable YoonityJS
		$this->YoonityJSConnector->deny();
		
		$groups = isset($this->request->query['groups']) ? $this->request->query['groups'] : [];
		$Acl = $this->AppAcl->Acl->adapter();
		$aroIDs = $Acl->Aro->find('list', [
			'conditions' => [
				'Aro.foreign_key' => $groups
			],
			'fields' => [
				'Aro.id'
			],
			'recursive' => -1
		]);
		$conflicts = $Acl->conflicts($aroIDs);
		
		$this->set(compact('conflicts'));
		return $this->render('../Elements/users/group_conflicts');
	}
}
