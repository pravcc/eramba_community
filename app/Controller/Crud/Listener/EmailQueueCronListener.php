<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('CronException', 'Cron.Error');

/**
 * Dashboard CRON listener that handles all tasks that needs to be processed during the CRON.
 */
class EmailQueueCronListener extends CronCrudListener
{
	/**
	 * Configured priority for hourly cron to flush emails as first hourly cron process.
	 * 
	 * @return array Events.
	 */
	public function implementedEvents() {
		return array(
			'Cron.hourly' => array('callable' => 'hourly', 'priority' => 15)
		);
	}

	/**
	 * Hourly CRON listener for Dashboards.
	 * 	
	 * @param  CakeEvent $event
	 */
	public function hourly(CakeEvent $event)
	{
		// flush emails always except 1am in the morning
		// @see DashboardCronListener class
		if (date('G') != '1') {
			$this->_flushEmails();
		}
	}

	/**
	 * Method initiate flushing of queued emails.
	 * 
	 * @return boolean True on success, False otherwise
	 */
	protected function _flushEmails()
	{
		if (!ErambaCakeEmail::sendQueue()) {
			throw new CronException(__('There was an error while flushing the email queue, we could not complete the process. Many times this happens when files (emails) have been deleted by a linux admin from the directory: eramba_v2/app/Vendor/queue/.'));
		}
	}

}
