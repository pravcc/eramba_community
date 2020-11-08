<?php
use Phinx\Migration\AbstractMigration;

class DashboardsUpdate2 extends AbstractMigration
{

    public function up()
    {
        $this->table('dashboard_kpis')
            ->removeColumn('default')
            ->update();


        $this->table('dashboard_kpis')
            ->addColumn('class_name', 'string', [
                'after' => 'id',
                'default' => null,
                'length' => 155,
                'null' => true,
            ])
            ->addColumn('category', 'integer', [
                'after' => 'type',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('dashboard_kpis')
            ->addColumn('default', 'integer', [
                'after' => 'type',
                'default' => '1',
                'length' => 3,
                'null' => false,
            ])
            ->removeColumn('class_name')
            ->removeColumn('category')
            ->update();
    }
}

