<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('LdapSyncModule', 'LdapSync.Lib');

/**
 * Ldap Sync CRON.
 */
class LdapSyncCronListener extends CronCrudListener
{
	/**
	 * LdapSyncModule
	 * @var LdapSyncModule
	 */
	protected $LdapsyncModule = null;

	public function implementedEvents()
	{
		return array(
			'Cron.beforeHandle' => 'beforeHandle',
			'Cron.hourly' => 'hourly'
		);
	}

	public function beforeHandle(CakeEvent $event)
	{
		$request = $this->_request();
		$model = $this->_model();
		$controller = $this->_controller();

		$this->LdapSyncModule = new LdapSyncModule();
	}

	/**
	 * Hourly CRON listener for Ldap Synchronization.
	 * 	
	 * @param  CakeEvent $event
	 * @return boolean True on success, False on failure.
	 */
	public function hourly(CakeEvent $event)
	{
		$ret = (boolean) $this->LdapSyncModule->synchronizeAll();
		// if (!$ret) {
		// 	throw new CronException(__('Ldap Synchronization failed.'));
		// }
	}

}
