<?php
App::uses('SystemLogsController', 'SystemLogs.Controller');

class LdapSynchronizationSystemLogsController extends SystemLogsController
{
	public $uses = ['LdapSync.LdapSynchronizationSystemLog'];

	public function beforeFilter()
	{
		parent::beforeFilter();

		$this->title = __('Ldap Synchronization Audit Trails');
		$this->subTitle = __('');
		
	}

	public function index()
	{
		if (AppModule::loaded('NotificationSystem')) {
			$this->Crud->addListener('NotificationSystem', 'NotificationSystem.NotificationSystem', [
				'useModel' => 'LdapSync.LdapSynchronization'
			]);
		}

		return parent::index();
	}
}
