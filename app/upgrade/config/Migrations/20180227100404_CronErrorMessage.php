<?php
use Phinx\Migration\AbstractMigration;

class CronErrorMessage extends AbstractMigration
{

    public function up()
    {

        $this->table('cron')
            ->addColumn('message', 'text', [
                'after' => 'url',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('cron')
            ->removeColumn('message')
            ->update();
    }
}

