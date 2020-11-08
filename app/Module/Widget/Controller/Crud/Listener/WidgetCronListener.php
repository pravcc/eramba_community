<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('CronException', 'Cron.Error');
App::uses('ClassRegistry', 'Utility');

/**
 * Widget CRON listener.
 */
class WidgetCronListener extends CronCrudListener
{
	public function beforeHandle(CakeEvent $event)
	{
	}

	public function daily(CakeEvent $event)
	{
		$controller = $this->_controller();

		$ret = true;

		App::uses('WidgetModule', 'Widget.Lib');

		$WidgetModule = new WidgetModule();

		$ret &= (bool) $WidgetModule->digestNotifications();
		
		if (!$ret) {
			throw new CronException(__('Widget cron processing failed'));
		}
	}

}
