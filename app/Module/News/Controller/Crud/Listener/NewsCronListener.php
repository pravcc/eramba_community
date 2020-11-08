<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('CronException', 'Cron.Error');
App::uses('ClassRegistry', 'Utility');
App::uses('NewsModule', 'News.Lib');

/**
 * News CRON listener.
 */
class NewsCronListener extends CronCrudListener
{
    public function beforeHandle(CakeEvent $event)
    {
    }

    public function hourly(CakeEvent $event)
    {
        $ret = true;

        $NewsModule = new NewsModule();

        $ret &= (bool) $NewsModule->readNews();
        
        if (!$ret) {
            throw new CronException(__('News cron processing failed'));
        }
    }
}