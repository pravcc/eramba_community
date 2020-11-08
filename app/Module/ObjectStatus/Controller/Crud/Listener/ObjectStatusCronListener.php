<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('ObjectStatusModule', 'ObjectStatus.Lib');
App::uses('CronException', 'Cron.Error');

/**
 * ObjectStatus CRON listener.
 */
class ObjectStatusCronListener extends CronCrudListener
{
	public function beforeHandle(CakeEvent $event)
	{
		$this->ObjectStatusModule = new ObjectStatusModule();
	}

	public function daily(CakeEvent $event)
	{
		if (!$this->ObjectStatusModule->syncAllStatuses()) {
			throw new CronException(__('ObjectStatus processing failed'));
		}
	}

}
