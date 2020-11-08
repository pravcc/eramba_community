<?php
App::uses('AppController', 'Controller');
App::uses('ThirdPartyAuditsModule', 'ThirdPartyAudits.Lib');

class ThirdPartyAuditsAppController extends AppController {
	public $components = [
		'Portal'
	];

	public $helpers = ['ThirdPartyAudits.ThirdPartyAudits'];
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->layout = 'default';
		$this->set('title_for_layout', __('Third Party Audits Portal'));
	}

	protected function _setupSecurity() {
		parent::_setupSecurity();

		$this->Security->csrfUseOnce = false;
	}

	protected function _setupAuthentication()
	{
		parent::_setupAuthentication();

		ThirdPartyAuditsModule::setAuthSessionKey();
		$this->Auth->authorize = false;
		$this->Auth->authError = false;
 
		$this->Auth->loginAction = array('controller' => 'thirdPartyAudits', 'action' => 'login', 'admin' => false, 'plugin' => 'thirdPartyAudits');
		$this->Auth->loginRedirect = array('controller' => 'thirdPartyAudits', 'action' => 'index', 'admin' => false, 'plugin' => 'thirdPartyAudits');
		$this->Auth->logoutRedirect = array('controller' => 'thirdPartyAudits', 'action' => 'login', 'admin' => false, 'plugin' => 'thirdPartyAudits');

		// Set redirect URL for OAuth
		$this->OauthGoogleAuth->setRedirectUrl($this->Auth->loginAction);

		$ldapAuth = $this->LdapConnectorAuthentication->getAuthData();
		if (empty($ldapAuth['LdapConnectorAuthentication']['auth_compliance_audit'])) {
			echo __('Third Party Audit portal is disabled. Please go to Eramba -> Settings -> Authentication to enable it.');
			exit;
		}
	}

	protected function _afterSetup() {
		parent::_afterSetup();

		$this->set('layout_headerPath', 'portal/header');
		$this->set('layout_toolbarPath', 'portal/toolbar');
	}

}
