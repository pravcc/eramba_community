<?php
use Phinx\Migration\AbstractMigration;

class CronTasks3 extends AbstractMigration
{

    public function up()
    {

        $this->table('cron_tasks')
            ->addIndex(
                [
                    'cron_id',
                ],
                [
                    'name' => 'idx_cron_id',
                ]
            )
            ->update();

        $this->table('cron_tasks')
            ->addForeignKey(
                'cron_id',
                'cron',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('cron_tasks')
            ->dropForeignKey(
                'cron_id'
            );

        $this->table('cron_tasks')
            ->removeIndexByName('idx_cron_id')
            ->update();
    }
}

