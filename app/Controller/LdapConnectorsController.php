<?php
App::uses('AppController', 'Controller');
App::uses('Hash', 'Utility');

class LdapConnectorsController extends AppController
{
	public $helpers = [
		'Html', 'Form'
	];
	public $components = [
		'Session', 'Paginator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Widget.Widget'
			]
		]
	];
	public $uses = array('LdapConnector');

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

		$this->Crud->enable(['index', 'add', 'edit', 'delete']);

		// lets do a quick ACL check on settings in general
		$this->Crud->on('beforeHandle', array($this, '_ldapBeforeHandle'));

		$this->Security->unlockedActions = array('testLdap');
		$this->Security->csrfCheck = false;
	}

	public function _ldapBeforeHandle(CakeEvent $e)
	{
	}

	public function _connectorsPaginate(CakeEvent $e)
	{
		$settings = &$e->subject->paginator->settings;
		$settings['contain'] = [
			'SecurityPolicy' => [
				'fields' => ['id']
			],
			'AwarenessProgram' => [
				'fields' => ['id']
			]
		];
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('SettingsSubSection', 'SettingsSubSection');

		$this->title = __('LDAP Connectors');

		$this->Crud->on('beforePaginate', array($this, '_connectorsPaginate'));

		return $this->Crud->execute();
	}

	public function delete($id = null)
	{
		$this->title = __('LDAP Connectors');
		$this->subTitle = __('Delete an Ldap Connector');

		return $this->Crud->execute();
	}

	public function add()
	{
		$this->title = __('Create an LDAP Connector');

		$this->Crud->on('beforeHandle', [$this, '_beforeHandle']);
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function edit($id = null)
	{
		$this->title = __('Edit an LDAP Connector');

		$this->Crud->on('beforeHandle', [$this, '_beforeHandle']);
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function _beforeHandle(CakeEvent $e)
	{
		$validateOnlySubmit = !empty($this->request->query['validate-only-submit']) ? true : false;
		unset($this->request->query['validate-only-submit']);

		$subject = $e->subject;
		$request = $subject->request;

		if ($validateOnlySubmit) {
			$subject->crud->action()->saveMethod('customValidateAssociated');
		}
	}

	public function _beforeRender(CakeEvent $event)
	{
		$this->_initTestButtonsInModal($event);
	}

	protected function _initTestButtonsInModal(CakeEvent $event)
	{
		$submitBtnConfig = $this->Modals->getConfig('footer.buttons.saveBtn.options');
		$submitBtnConfig['data-yjs-on-modal-success'] = 'none';
		$submitBtnConfig['data-yjs-datasource-url'] = $this->viewVars['formUrl'] . '&validate-only-submit=true';
		
		$formName = $this->viewVars['formName'];
		$this->Modals->addFooterButton(__('Test Connection'), Hash::merge($submitBtnConfig, [
			'id' => 'test-ldap',
			'class' => 'btn btn-default',
			'style' => 'display:none',
			'data-yjs-on-success-reload' => '#test-ldap-yjs-request'
		]), 'testLdapBtn', true, 'button', 'before-saveBtn');
		$this->Modals->addFooterButton(__('Test Getting Members of a Group'), Hash::merge($submitBtnConfig, [
			'id' => 'test-ldap-user',
			'class' => 'btn btn-default',
			'style' => 'display:none',
			'data-yjs-on-success-reload' => '#test-ldap-user-yjs-request'
		]), 'testLdapUserBtn', true, 'button', 'after-testLdapBtn');
		$this->Modals->addFooterButton(__('Test Getting List Of Groups'), Hash::merge($submitBtnConfig, [
			'id' => 'test-ldap-group',
			'class' => 'btn btn-default',
			'style' => 'display:none',
			'data-yjs-on-success-reload' => '#test-ldap-group-yjs-request'
		]), 'testLdapGroupBtn', true, 'button', 'after-testLdapUserBtn');
	}

	public function testLdapForm($type)
	{
		$this->allowOnlyAjax();

		$testLdapFormName = 'LdapConnectorTestForm';
		$url = '';
		$fieldFriendlyName = '';
		$fieldName = '';
		if ($type === 'user') {
			$data = $this->request->data['LdapConnector'];
			if ($data['type'] === 'authenticator') {
				if (strpos($data['ldap_auth_filter'], "%USERNAME%") !== false || strpos($data['ldap_auth_filter'], "%username%") !== false) {
					$url = '/ldapConnectors/testLdap';
					$fieldFriendlyName = __('Enter <strong>%USERNAME%</strong> value');
					$fieldName = 'data[custom][_ldap_auth_filter_username_value]';
				} else {
					return $this->testLdap();
				}
			}
		} else if ($type === 'group') {
			$url = '/ldapConnectors/testLdap/listUsers';
			$fieldFriendlyName = __('Enter group name');
			$fieldName = 'data[additional][groupName]';
		}

		$this->Ajax->initModal('normal', __("Test LDAP"));
		$this->Modals->addFooterButton(__('Test'), [
			'class' => 'btn btn-primary',
			'data-yjs-request' => 'ldapConnectors/ldapConnectorTestRequest',
			'data-yjs-event-on' => 'click',
			'data-yjs-datasource-url' => $url,
			'data-yjs-target' => 'modal',
			'data-yjs-modal-id' => null,
			'data-yjs-on-modal-success' => 'none',
			'data-yjs-on-modal-failure' => 'close',
			'data-yjs-forms' => 'LdapConnectorSectionAddForm|LdapConnectorSectionEditForm|' . $testLdapFormName
		]);

		$this->set(compact('testLdapFormName', 'fieldFriendlyName', 'fieldName'));
	}

	public function testLdap($testType = null)
	{
		$this->allowOnlyAjax();

		$this->Ajax->initModal('normal', __("LDAP Results"));
		
		$data = $this->request->data['LdapConnector'];
		$additionalData = isset($this->request->data['additional']) && is_array($this->request->data['additional']) ? $this->request->data['additional'] : [];

		//
		// If user set different value white testing, this value will be used
		if (isset($this->request->data['custom']['_ldap_auth_filter_username_value'])) {
			$data['_ldap_auth_filter_username_value'] = $this->request->data['custom']['_ldap_auth_filter_username_value'];
		}
		//

		try {
			$ldap = $this->Components->load('LdapConnectorsMgt');
			$ldap->initialize($this);

			$LdapConnector = $ldap->getConnector($data);
			$ldapConnection = $LdapConnector->connect();

			$limit = 200;
			$LdapConnector->setSizeLimit($limit);

			$options = am(array(
				'testType' => $testType
			), $additionalData);

			$results = $LdapConnector->getTest($options);

			$this->set('ldapConnection', $ldapConnection);
			$this->set('results', $results);
			$this->set('limit', $limit);

			$this->render('/Elements/ldapConnectors/test');
		} catch(Exception $e) {
			$this->layout = false;
			$this->autoRender = false;

			$this->Ajax->setState('error');
			$this->Ajax->addNotification($e->getMessage(), 'error');
		}
	}

}
