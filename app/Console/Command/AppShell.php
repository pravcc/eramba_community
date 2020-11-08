<?php
/**
 * AppShell file
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Shell', 'Console');
App::uses('AuthComponent', 'Controller/Component');
App::uses('AppAuthComponent', 'Controller/Component');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Model', 'Model');
App::uses('Validation', 'Utility');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
	public function initialize() {
		// add App Modules for custom baking using original shell
		if ($this->name == 'Bake') {
			$this->tasks[] = 'Module';
		}

		parent::initialize();

		$this->_setBaseUrl();
	}

	public function startup() {

	}

	/**
	 * Set full base url from settings.
	 */
	protected function _setBaseUrl()
	{
		if (!empty(Configure::read('Eramba.Settings.CRON_URL'))) {
			if (!Validation::url(Configure::read('Eramba.Settings.CRON_URL'))) {
				$this->out('<warning>The URL you configured appears to be not valid URL. We will apply it anyway.</warning>');
			}

			Configure::write('App.fullBaseUrl', Configure::read('Eramba.Settings.CRON_URL'));
		}
	
		if (Configure::read('App.fullBaseUrl') == 'http://localhost') {
			$this->out('<warning>You should configure Cron URL in Settings.</warning>');
		}
	}

	protected function _loginAdmin() {
		AuthComponent::$sessionKey = false;

		// login admin
		$comp = (new ComponentCollection());
		$comp->init(new Controller());
		$this->Auth = (new AuthComponent($comp, []));

		if ($this->Auth->user() != null) {
			return true;
		}

		$modelConfig = ['table' => 'users', 'name' => 'BootstrapUser', 'ds' => 'default'];
		$User = (new Model($modelConfig));
		
		$User->cacheSources = false;

		$user = $User->find('first', [
			'conditions' => [
				'BootstrapUser.id' => ADMIN_ID
			],
			'recursive' => -1
		]);
		
		$this->Auth->login($user['BootstrapUser']); 
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();

		// add Modules also into the list of commands when using Bake shell 
		if ($this->name == 'Bake') {
			$parser->addSubcommand('module', array(
				'help' => __d('cake_console', 'Bake a new App Module which is extended Plugin class having customized base to work on.'),
				'parser' => $this->Module->getOptionParser()
			));
		}

		return $parser;
	}
}
