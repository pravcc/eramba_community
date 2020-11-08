<?php
use Phinx\Migration\AbstractMigration;

class DashboardThresholds extends AbstractMigration
{

    public function up()
    {
        $this->table('dashboard_kpi_thresholds')
            ->addColumn('kpi_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('color', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('min', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('max', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('percentage', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'kpi_id',
                ]
            )
            ->create();

        $this->table('dashboard_kpi_thresholds')
            ->addForeignKey(
                'kpi_id',
                'dashboard_kpis',
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
        $this->table('dashboard_kpi_thresholds')
            ->dropForeignKey(
                'kpi_id'
            );

        $this->dropTable('dashboard_kpi_thresholds');


    }
}

