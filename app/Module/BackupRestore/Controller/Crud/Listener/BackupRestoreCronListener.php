<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('CronException', 'Cron.Error');
App::uses('BackupRestoreCronLib', 'BackupRestore.Lib');

/**
 * BackupRestore CRON listener that handles database backup during the CRON.
 */
class BackupRestoreCronListener extends CronCrudListener
{
	/**
	 * Before handle callback should handle preloading of classes needed within this listener process.
	 * 
	 * @param  CakeEvent $event
	 * @return void
	 * @throws CronException     If there has been an issue while preparing this listener process for CRON Job.
	 */
	public function beforeHandle(CakeEvent $event)
	{
		$this->BackupRestoreCronLib = new BackupRestoreCronLib();
	}

	/**
	 * Daily CRON makes a backup of the database.
	 * 	
	 * @param  CakeEvent $event
	 * @return boolean True on success, False on failure.
	 */
	public function daily(CakeEvent $event)
	{
		if (!$this->BackupRestoreCronLib->dailyBackup()) {
			throw new CronException(__('Backup of your database failed'));
		}
	}

}
