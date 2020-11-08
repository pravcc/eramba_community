<?php
use Phinx\Migration\AbstractMigration;

class CronTasks4 extends AbstractMigration
{

    public function up()
    {

        $this->table('cron_tasks')
            ->addColumn('message', 'text', [
                'after' => 'execution_time',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('cron_tasks')
            ->removeColumn('message')
            ->update();
    }
}

