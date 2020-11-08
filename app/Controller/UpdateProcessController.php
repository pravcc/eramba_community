<?php
App::uses('AutoUpdateLib', 'Lib');
App::uses('AuthComponent', 'Controller/Component');

class UpdateProcessController extends Controller
{
	public $helpers = ['Html', 'Form'];
	public $components = [
		'AppConfig',
		'Session'
	];
	public $uses = false;

	public function beforeFilter()
	{
		$this->AppConfig->ensureOffloadSSL();

		parent::beforeFilter();
	}

	public function update()
	{
		$this->layout = false;

		if (!isAdmin(AuthComponent::user())) {
			$this->Session->setFlash(__('Only accounts members of the admin group are allowed to update the system.'), FLASH_ERROR);
			return $this->redirect(array('controller' => 'pages', 'action' => 'welcome'));
		}

		ignore_user_abort(true);
		// set_time_limit(600); //10 min

		$AutoUpdateLib = new AutoUpdateLib();
		$update = $AutoUpdateLib->update();
		if ($AutoUpdateLib->hasError()) {
			$this->set('errorMessage', $AutoUpdateLib->getErrorMessage());
		}
		else {
			$this->set('successMessage', __('Successfuly updated.'));
		}

		$this->set('update', $AutoUpdateLib->check());

		$this->render('/Elements/updates/updateWidget');
	}

	public function syncAcl()
	{
		$this->layout = false;

		ClassRegistry::init('Setting')->syncAcl(true);

		echo json_encode(['synchronized' => true]);
		exit;
	}
}
