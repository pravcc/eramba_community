<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('Cron', 'Model');
App::uses('CronModule', 'Cron.Lib');

/**
 * Cron user session handler.
 */
class CronUserSessionListener extends CronCrudListener {

	/**
	 * Store login session data about the current logged in user so we can safely login admin user during runtime,
	 * to keep cron process dry. In the end, this value, in case when its not null, is used to relog actual user.
	 * 
	 * @var null
	 */
	protected $_loginSession = null;

	/**
	 * Specifically prioritized callbacks for this listener, as it is the primary core configuration
	 * for CRON jobs and some callbacks should be executed as first before the other listeners,
	 * and some last just after the others because they require to summarize what all has already happened.
	 * 
	 * @see    inspired by DebugKitListener class
	 * @return void
	 */
	public function implementedEvents() {
		return array(
			'Cron.beforeHandle' => array('callable' => 'beforeHandle', 'priority' => 2),
			'Cron.beforeJob' => array('callable' => 'beforeJob', 'priority' => 2),
			'Cron.afterJob' => array('callable' => 'afterJob', 'priority' => 2)
		);
	}

	/**
	 * Set some variables before the cron job begins.
	 */
	public function beforeHandle(CakeEvent $event) {
		$this->_loginSession = $this->_controller()->Auth->user();
	}

	/**
	 * Manually login admin user for a case where objects are automatically created
	 * and expects and owner user for visualisations or ACL.
	 * 
	 * @return void
	 */
	protected function _switchLoginSession($switchToAdmin) {
		$controller = $this->_controller();

		if ($switchToAdmin === false) {
			// lets logout the user completely to avoid security issues
			return $controller->Auth->logout();
		}
		else {
			$user = ClassRegistry::init('User')->find('first', [
				'conditions' => [
					'User.id' => ADMIN_ID
				],
				'contain' => [
					'Group'
				]
			]);

			$user['User']['Group'] = $user['Group'];
			$user = $user['User'];
		}

		// When user is logged in for cron purposes, he must not be asked for changing his default password
		$user['default_password'] = 0;
		
		$controller->Auth->login($user);
		$controller->logged = $controller->Auth->user();

		return true;
	}

	/**
	 * Startup the CRON job by configuring it as running at the moment.
	 */
	public function beforeJob(CakeEvent $event) {
		// switch to admin user account for cron jobs
		$this->_switchLoginSession(true);
	}

	/**
	 * Prioritized callback that saves the record about current cron job's status.
	 */
	public function afterJob(CakeEvent $event) {
		// revert login session
		$this->_switchLoginSession(false);
	}

}
