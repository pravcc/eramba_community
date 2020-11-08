<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('CakeEvent', 'Event');

class UsersCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

	public function beforeLayoutToolbarRender($event)
	{
		$this->_setToolbar();
	}

	protected function _setToolbar()
	{
		//
		// Add Settings and LDAP synchronization dropdown
		if (empty($this->LayoutToolbar->config('settings'))) {
			$this->LayoutToolbar->addItem(__('Settings'), '#', [
				'slug' => 'settings',
			]);
		}

		$this->LayoutToolbar->addItem(__('Account Sync - LDAP'), '#', [
			'parent' => 'settings',
			'slug' => 'account_sync_ldap',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'assetClassifications',
				'action' => 'index'
			])
		]);
		//
		
		//
		// Add add new button
		$toolbarLdapSyncs = [];
		$toolbarLdapSyncs[] = [];
		$this->LayoutToolbar->addItem(__('Add new'), '#', [
			'slug' => 'account_sync_ldap_add_new',
			'parent' => 'account_sync_ldap',
			'icon' => 'plus2',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'users',
				'action' => 'addLdapSync'
			])
		]);
		//

		//
		// Add LDAP synchronizations
		$ldapSyncs = [
			[
				'name' => 'test1',
			],
			[
				'name' => 'test2'
			]
		];
		foreach ($ldapSyncs as $ldapSync) {
			$name = $ldapSync['name'];
			$this->LayoutToolbar->addItem($name, '#', [
				'slug' => 'ldap_sync-' . $name,
				'parent' => 'account_sync_ldap',
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => 'modal',
				'data-yjs-event-on' => 'click',
				'data-yjs-modal-size-width' => 80,
				'data-yjs-datasource-url' =>  Router::url([
					'controller' => 'assetMediaTypes',
					'action' => 'index'
				])
			]);
		}
		//
	}
}
