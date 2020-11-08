<?php
use Phinx\Migration\AbstractMigration;

class CronTasks2 extends AbstractMigration
{

    public function up()
    {

        $this->table('cron_tasks')
            ->changeColumn('execution_time', 'float', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('cron_tasks')
            ->changeColumn('execution_time', 'datetime', [
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }
}

