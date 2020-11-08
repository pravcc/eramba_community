<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('CronException', 'Cron.Error');
App::uses('AutoUpdateLib', 'Lib');

/**
 * AutoUpdate CRON listener.
 */
class AutoUpdateCronListener extends CronCrudListener
{
	public function beforeHandle(CakeEvent $event)
	{
		$this->_ensureLibrary();
	}

	protected function _ensureLibrary()
	{
		// short-hand call to load a component
		$this->AutoUpdate = new AutoUpdateLib();
	}

	public function daily(CakeEvent $event)
	{
		$this->AutoUpdate->skipHealthCheck = [
			'cron-hourly',
			'cron-daily',
			'pdf-path-to-bin'
		];
		
		$this->AutoUpdate->check();
		if ($this->AutoUpdate->hasError()) {
			throw $this->AutoUpdate->getLastCronException();
		}
	}

}
