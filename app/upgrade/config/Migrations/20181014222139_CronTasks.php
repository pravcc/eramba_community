<?php
use Phinx\Migration\AbstractMigration;

class CronTasks extends AbstractMigration
{

    public function up()
    {

        $this->table('cron_tasks')
            ->addColumn('cron_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('task', 'string', [
                'default' => '',
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('execution_time', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('completed', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('cron')
            ->addColumn('completed', 'datetime', [
                'after' => 'created',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('cron')
            ->removeColumn('completed')
            ->update();

        $this->dropTable('cron_tasks');
    }
}

