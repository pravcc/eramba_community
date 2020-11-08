<?php
App::uses('AppController', 'Controller');
App::uses('Hash', 'Utility');

class LdapConnectorAuthenticationsController extends AppController
{
	public $helpers = [
		'Html', 'Form'
	];
	public $components = [
		'Session', 'Paginator'
	];

	/**
	 * By default subtitle is disabled.
	 * 
	 * @var boolean|string
	 */
	public $subTitle = false;

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
		parent::beforeFilter();

		$this->Crud->enable(['edit']);
	}

	public function edit()
	{
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-success-reload', '');

		$this->Crud->on('beforeRender', array($this, '_authenticationRender'));

		return $this->Crud->execute(null, ['id' => 1]);
	}

	public function _authenticationRender(CakeEvent $e)
	{
		$this->loadModel('LdapConnector');

		$connectors = $this->LdapConnector->find('all', array(
			'recursive' => -1
		));

		$authenticators = $this->LdapConnector->find('list', array(
			'conditions' => array(
				'LdapConnector.type' => 'authenticator'
			),
			'fields' => array('id', 'name'),
			'recursive' => -1
		));

		$this->loadModel('OauthConnector');
		$oauthGoogleConnectors = $this->OauthConnector->find('list', array(
			'fields' => array('id', 'name'),
			'conditions' => array(
				'OauthConnector.provider' => OauthConnector::PROVIDER_GOOGLE,
				'OauthConnector.status' => OauthConnector::STATUS_ACTIVE
			),
			'recursive' => -1
		));

		$this->loadModel('SamlConnector');
		$samlConnectors = $this->SamlConnector->find('list', [
			'fields' => [
				'id', 'name'
			],
			'conditions' => [
				'SamlConnector.status' => SamlConnector::STATUS_ACTIVE
			],
			'recursive' => -1
		]);

		$this->set('authenticators', $authenticators);
		$this->set('oauthGoogleConnectors', $oauthGoogleConnectors);
		$this->set('samlConnectors', $samlConnectors);

		$this->set('general_auth_default', LdapConnectorAuthentication::GENERAL_AUTH_DEFAULT);
		$this->set('general_auth_ldap', LdapConnectorAuthentication::GENERAL_AUTH_LDAP);
		$this->set('general_auth_oauth_google', LdapConnectorAuthentication::GENERAL_AUTH_OAUTH_GOOGLE);
		$this->set('general_auth_saml', LdapConnectorAuthentication::GENERAL_AUTH_SAML);
	}
}
