<?php
use Phinx\Migration\AbstractMigration;

class DashboardsUpdate3 extends AbstractMigration
{

    public function up()
    {
        $this->table('dashboard_kpis')
            ->addColumn('status', 'integer', [
                'after' => 'value',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('dashboard_kpis')
            ->removeColumn('status')
            ->update();
    }
}

