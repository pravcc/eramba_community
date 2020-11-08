<?php
use Phinx\Migration\AbstractMigration;

class DashboardLogs extends AbstractMigration
{

    public function up()
    {

        $this->table('dashboard_logs')
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();
    }

    public function down()
    {

        $this->dropTable('dashboard_logs');
    }
}

