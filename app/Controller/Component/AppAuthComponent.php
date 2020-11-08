<?php

App::uses('AuthComponent', 'Controller/Component');
App::uses('Hash', 'Utility');
App::uses('Portal', 'Model');
App::uses('VendorAssessmentsModule', 'VendorAssessments.Lib');
App::uses('AccountReviewsModule', 'AccountReviews.Lib');
 
class AppAuthComponent extends AuthComponent
{
	protected $portalAllowed = false;
	protected $checkPortals = [
		'main', 'vendor_assessments', 'account_reviews'
	];
	protected $authUsersSessionKey = 'Auth.AuthUsersFromAllPortals';

	public function getAuthenticateObject($object)
	{
		foreach ($this->_authenticateObjects as $class) {
			$className = get_class($class);
			if ($className == $object) {
				return $class;
			}
		}
		
		return false;
	}

	public function login($user = null) 
	{
		$this->User = ClassRegistry::init('User');

		if (!$this->_checkPortal()) {
			$this->flash(__("Your account has not been allowed access to this portal, contact the administrator"));
			return $this->loggedIn();
		}

		if (parent::login($user)) {
			$groupsTemp = $this->User->find('first', array(
				'conditions' => array(
					'User.id' => $this->user('id')
				),
				'contain' => array(
					'Group' => array(
						'fields' => array(
							'Group.id'
						)
					)
				)
			));

			$groups = Hash::extract($groupsTemp, 'Group.{n}.id');
			$user = $this->user();
			$user['Groups'] = $groups;

			$loginRet = parent::login($user);

			$this->storeAuthUsersInSession();

			return $loginRet;
		}
 
		return $this->loggedIn();
	}

	public function startup(Controller $controller)
	{
		if (empty($this->request) || empty($this->response))
		{
			$this->initialize($controller);
		}

		parent::startup($controller);
	}

	protected function _checkPortal()
	{
		$controller = $this->request['controller'];
		$Portal = ClassRegistry::init('Portal');

		$portalTemp = $Portal->find('first', [
			'fields' => [
				'id', 'name'
			],
			'conditions' => [
				'Portal.controller' => $controller
			]
		]);

		$portalName = Hash::get($portalTemp, 'Portal.name');

		//
		// Check if portal is listed in portals which should be checked and if is not, allow portal
		if (empty($portalName) || !in_array($portalName, $this->checkPortals)) {
			$this->portalAllowed = true;
			return true;
		}
		//
		
		$login = null;
		$tempUserData = $this->_getUserData();
		if (isset($tempUserData['login'])) {
			$login = $tempUserData['login'];
		} else if (isset($this->request->data['User']['login'])) {
			$login = $this->request->data['User']['login'];
		} else {
			$this->portalAllowed = false;
			return false;
		}
		
		//
		// If User Model was not initialized yet, initialize it now
		if (empty($this->User)) {
			$this->User = ClassRegistry::init('User');
		}
		//
		
		$user = $this->User->find('first', [
			'conditions' => [
				'User.login' => $login
			],
			'contain' => [
				'Portal' => [
					'fields' => [
						'Portal.id'
					]
				]
			]
		]);

		if (!empty($user)) {
			$portalId = Hash::get($portalTemp, 'Portal.id');
			$userPortals = Hash::extract($user, 'Portal.{n}.id');

			if ($user['User']['id'] == ADMIN_ID || 
				(!empty($portalId) && !empty($userPortals) && in_array($portalId, $userPortals))) {
				$this->portalAllowed = true;
				return true;
			}
		} else {
			$this->portalAllowed = true;
			return true;
		}

		$this->portalAllowed = false;
		return false;
	}

	protected function _getUserData()
	{
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		foreach ($this->_authenticateObjects as $auth) {
			$result = null;
			if (method_exists($auth, 'checkUser')) {
				$result = $auth->checkUser($this->request, $this->response);
			}

			if (!empty($result) && is_array($result)) {
				return $result;
			}
		}

		return false;
	}

	public function portalAllowed()
	{
		return $this->portalAllowed;
	}

	protected function _unauthenticated(Controller $controller) {
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		$auth = $this->_authenticateObjects[count($this->_authenticateObjects) - 1];
		if ($auth->unauthenticated($this->request, $this->response)) {
			return false;
		}

		if ($this->_isLoginAction($controller)) {
			if (empty($controller->request->data)) {
				if (!$this->Session->check('Auth.redirect') && env('HTTP_REFERER')) {
					$this->Session->write('Auth.redirect', $controller->referer(null, true));
				}
			}
			return true;
		}

		if (!$controller->request->is('ajax') && !$controller->request->is('json')) {
			$this->flash($this->authError);
			$this->Session->write('Auth.redirect', $controller->request->here(false));
			$controller->redirect($this->loginAction);
			return false;
		}

		throw new ForbiddenException('Unlogged user. Your session probably expired.');
	}

	protected function getAuthUsersFromAllPortals($userId = null, $session = true)
	{
		if ($session && $this->Session->check($this->authUsersSessionKey)) {
			$allPortalAuth = $this->Session->read($this->authUsersSessionKey);
		} else {
			$allPortalAuth['main'] = $this->Session->read('Auth.User');

			if (AppModule::loaded('VendorAssessments')) {
				$allPortalAuth['vendor_assessments'] = $this->Session->read(VendorAssessmentsModule::getSessionKey());
			}

			if (AppModule::loaded('AccountReviews')) {
				$allPortalAuth['account_reviews'] = $this->Session->read(AccountReviewsModule::getSessionKey());
			}
		}

		$authUsers = [];
		$allowedKeys = ['id', 'local_account', 'default_password'];
		foreach ($allPortalAuth as $portalName => $portalUser) {
			if (!empty($portalUser) && (empty($userId) || $portalUser['id'] == $userId)) {
				$authUsers[$portalName] = [];
				foreach ($allowedKeys as $key) {
					$authUsers[$portalName][$key] = $portalUser[$key];
				}
			}
		}

		return $authUsers;
	}

	protected function storeAuthUsersInSession($authUsers = null)
	{
		if ($authUsers === null) {
			$authUsers = $this->getAuthUsersFromAllPortals(null, false);
		}

		$this->Session->write($this->authUsersSessionKey, $authUsers);
	}

	protected function removeAuthUsersFromSession()
	{
		$this->Session->delete($this->authUsersSessionKey);
	}

	public function setDefaultPasswordChange($userId, $val)
	{
		$authUsers = $this->getAuthUsersFromAllPortals();
		foreach ($authUsers as $key => $user) {
			if ($user['id'] == $userId) {
				$authUsers[$key]['default_password'] = $val == 1 ? 1 : 0;
			}
		}

		$this->storeAuthUsersInSession($authUsers);
	}

	public function isPasswordChangeRequired($userId)
	{
		$alreadyChanged = false;
		$changeRequired = false;
		$authUsers = $this->getAuthUsersFromAllPortals($userId);
		foreach ($authUsers as $user) {
			if ($user['id'] == $userId && $user['local_account'] == 1) {
				if ($user['default_password'] == 1) {
					$changeRequired = true;
				} else {
					$alreadyChanged = true;
				}
			}
		}

		return $changeRequired && !$alreadyChanged;
	}

	public function isUserLoggedInOnAnyPortal($userId)
	{
		$authUsers = $this->getAuthUsersFromAllPortals($userId);
		return !empty($authUsers) ? true : false;
	}
}
