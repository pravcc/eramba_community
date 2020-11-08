<?php
use Phinx\Migration\AbstractMigration;

class DashboardsUpdate1 extends AbstractMigration
{

    public function up()
    {

        $this->table('dashboard_kpis')
            ->addColumn('default', 'integer', [
                'after' => 'type',
                'default' => '1',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('dashboard_kpis')
            ->removeColumn('default')
            ->update();
    }
}

