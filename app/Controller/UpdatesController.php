<?php
App::uses('Configure', 'Core');
App::uses('AutoUpdateLib', 'Lib');

class UpdatesController extends AppController {
	public $helpers = array('Html', 'Form', 'SettingsSubSection');
	public $components = array('Session');
	public $uses = [
		'Update',
		'BackupRestore.BackupRestore'
	];

	public function beforeFilter()
	{
		parent::beforeFilter();

		if (!isAdmin($this->logged)) {
			$this->Session->setFlash(__('Only accounts members of the admin group are allowed to update the system.'), FLASH_ERROR);
			$this->redirect(array('controller' => 'pages', 'action' => 'welcome'));
		}
	}

	public function index()
	{
		$this->set('title_for_layout', __('Available updates'));
		$this->set('subtitle_for_layout', __('Use this functionality to update your system'));

		$this->set('settingsAdditionalBreadcrumbs', [
			0 => [
				'name' => __('Settings'),
				'link' => Router::url([
					'controller' => 'Settings',
					'action' => 'index',
					'plugin' => false,
					'prefix' => false,
					'admin' => false
				]),
				'options' => [
					'prepend' => true
				]
			]
		]);

		Cache::delete('server_response', 'updates');

		if (!Configure::read('Eramba.offline')) {
			$AutoUpdateLib = new AutoUpdateLib();
			$update = $AutoUpdateLib->check();
			if ($AutoUpdateLib->hasError()) {
				$this->set('errorMessage', $AutoUpdateLib->getErrorMessage());
			}

			$this->set('update', $update);
		}
	}
}
