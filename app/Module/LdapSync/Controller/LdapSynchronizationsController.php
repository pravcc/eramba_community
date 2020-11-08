<?php
App::uses('LdapSyncAppController', 'LdapSync.Controller');
App::uses('LdapSyncModule', 'LdapSync.Lib');
App::uses('FormReloadListener', 'Controller/Crud/Listener');
App::uses('ConnectionManager', 'Model');

class LdapSynchronizationsController extends LdapSyncAppController
{
	public $helpers = [];
	public $components = [
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AppIndex',
				],
				'add' => [
					'className' => 'AppAdd',
				],
				'edit' => [
					'className' => 'AppEdit',
				],
				'delete' => [
					'className' => 'AppDelete',
				]
			]
		]
	];

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
		$this->Crud->enable(['add', 'edit', 'delete']);

		parent::beforeFilter();

		$this->Security->csrfCheck = false;

		$this->LdapSyncModule = new LdapSyncModule();
	}

	public function add()
	{
		$this->addEditCommonProcesses();

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function edit($id = null)
	{
		$this->addEditCommonProcesses();

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	protected function addEditCommonProcesses()
	{
		$this->Crud->on('beforeSave', function(CakeEvent $event)
		{
			$data = array_key_exists('LdapSynchronization', $this->request->data) ? $this->request->data['LdapSynchronization'] : null;

			if (empty($data)) {
				$this->Flash->error(__('We couldn\'t identify LDAP Synchronization for testing'));
			}

			if (!$this->LdapSyncModule->testSync($data)) {
				$this->Crud->action($this->request->action)->config('messages.error.text', __('LDAP Synchronization test failed. We couldn\'t retrieve any valid user from selected LDAP group and authenticator.'));
				$event->subject->customStopped = true;
			}
		});
	}

	public function _beforeRender(CakeEvent $event)
	{
		if ($this->_FieldDataCollection->has('ldap_group')) {
			$groupConnectorId = null;
			if ($this->request->action === 'edit') {
				$data = $this->LdapSynchronization->find('first', [
					'conditions' => [
						'LdapSynchronization.id' => $this->request->params['pass'][0]
					]
				]);

				if (!empty($data)) {
					$groupConnectorId = $data['LdapSynchronization']['ldap_group_connector_id'];
				}
			}

			if (FormReloadListener::isFormReload('ldap_group_connector_id') ||
				!empty($this->request->data['LdapSynchronization']['ldap_group_connector_id'])) {
				$groupConnectorId = $this->request->data['LdapSynchronization']['ldap_group_connector_id'];
			}

			if ($groupConnectorId !== null) {
				$ldapGroupField = $this->_FieldDataCollection->get('ldap_group');
				$ldapGroupField->config('options', $this->LdapSyncModule->getLdapGroupsList($groupConnectorId));
			}
		}
	}

	public function delete($id = null)
	{
		return $this->Crud->execute();
	}

	public function forceSync()
	{
		$syncs = $this->LdapSyncModule->synchronizeAll();
		$results = $this->LdapSyncModule->getUserStatuses();

		$this->Ajax->initModal('normal', __("LDAP Force Synchronization Completed"));

		$this->Modals->changeConfig('footer.buttons.closeBtn.options.data-yjs-on-complete', '#main-content');

		$this->set(compact('syncs', 'results'));

		$this->render('LdapSync./Elements/ldap_sync_simulation');
	}

	public function simulateSync()
	{
		$dataSource = ConnectionManager::getDataSource('default');
		$dataSource->begin();

		$syncs = $this->LdapSyncModule->synchronizeAll();
		$results = $this->LdapSyncModule->getUserStatuses();

		$dataSource->rollback();

		$this->Ajax->initModal('normal', __("LDAP Synchronization Simulation"));

		$this->set(compact('syncs', 'results'));

		$this->render('LdapSync./Elements/ldap_sync_simulation');
	}
}
