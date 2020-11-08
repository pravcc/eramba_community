<?php
use Phinx\Migration\AbstractMigration;

class CronTasks5 extends AbstractMigration
{

    public function up()
    {

        $this->table('cron')
            ->changeColumn('status', 'string', [
                'default' => 'success',
                'limit' => 128,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        // $this->table('cron')
        //     ->changeColumn('status', 'string', [
        //         'default' => 'success',
        //         'length' => null,
        //         'null' => true,
        //     ])
        //     ->update();
    }
}

