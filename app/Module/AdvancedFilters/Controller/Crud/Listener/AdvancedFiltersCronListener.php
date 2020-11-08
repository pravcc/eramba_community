<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('AdvancedFiltersCronLib', 'AdvancedFilters.Lib');

class AdvancedFiltersCronListener extends CronCrudListener
{

	public function beforeHandle(CakeEvent $event)
	{
		$this->AdvancedFiltersCronLib = new AdvancedFiltersCronLib();
	}

	public function daily(CakeEvent $event)
	{
		if (!$this->AdvancedFiltersCronLib->execute()) {
			throw new CronException($this->AdvancedFiltersCronLib->getErrorMessages(true));
		}
		else {
			$this->AdvancedFiltersCronLib->assignCronIdToRecords($event->subject->cronId);
		}
	}

}
