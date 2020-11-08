<?php
App::uses('File', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('Component', 'Controller');
App::uses('AuthComponent', 'Controller/Component');

class PortalComponent extends Component {
	public $components = array('SamlAuth', 'OauthGoogleAuth', 'Session', 'Auth', 'Flash', 'Translations.Translations');
	public $settings = array();

	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (empty($this->settings)) {
			$this->settings = array(
				'sessionKey' => null
			);
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->controller->helpers[] = 'Portal';
	}

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * General login method that uses default eramba authentication rules.
	 * 
	 * @param  array  $options Optional rules.
	 * @return CakeResponse
	 */
	public function login($options = []) {
		$options = array_merge([
			'loginTitle' => __('Sign In to your Account')
		], $options);

		$this->controller->layout = 'login';
		$this->controller->set('loginTitle', $options['loginTitle']);

		if ($this->controller->logged != null) {
			$this->controller->redirect($this->Auth->loginRedirect);
		}

		$showForgotPasswordBtn = false;

		if ($this->controller->request->is('post') || isset($this->controller->request->query['authuser'])) {
			if ($this->controller->BruteForce->check()) {
				if ($this->Auth->login()) {
					$userId = $this->Auth->user('id');

					$event = new CakeEvent('Portal.afterLogin', [
						'success' => true,
						'id' => $userId
					]);
					$this->controller->getEventManager()->dispatch($event);

					return $this->controller->redirect($this->Auth->redirect());
				}
				else {
					$event = new CakeEvent('Portal.afterLogin', [
						'success' => false
					]);
					$this->controller->getEventManager()->dispatch($event);

					if ($this->Auth->portalAllowed()) {
						$errorMsg = __('The system was unable to log you in. Check that your username and password are typed correctly.');

						$showForgotPasswordBtn = true;

						$ldapErr = $this->controller->_getLdapLoginError();
						if (!empty($ldapErr)) {
							$errorMsg = $ldapErr;
						}

						$this->Flash->error($errorMsg);
					}
				}
			}
			else {
				$this->controller->Session->setFlash($this->controller->BruteForce->getMessage(), FLASH_ERROR);
			}
		}

		$this->controller->set('model', 'User');

		$this->Translations->setAvailableTranslations();

		$this->controller->set('showForgotPasswordBtn', $showForgotPasswordBtn);
		$this->controller->set("OauthGoogleAllowed", $this->OauthGoogleAuth->isOauthGoogleAllowed());
		$this->controller->set("OauthGoogleAuthUrl", $this->OauthGoogleAuth->getSanitizedAuthUrl());
		$this->controller->set("samlAuthAllowed", $this->SamlAuth->isSamlAuthAllowed());
		$this->controller->set("samlAuthUrl", $this->SamlAuth->getSamlAuthUrl());

		return $this->controller->render('../Portal/login');
	}

	public function logout() {
		$this->OauthGoogleAuth->logout();
		return $this->controller->redirect($this->Auth->logout());
	}

}
