<?php
use Phinx\Migration\AbstractMigration;

class DashboardsMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('dashboard_kpi_attributes')
            ->addColumn('kpi_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('foreign_key', 'string', [
                'default' => '',
                'limit' => 128,
                'null' => false,
            ])
            ->addIndex(
                [
                    'kpi_id',
                ]
            )
            ->create();

        $this->table('dashboard_kpi_logs')
            ->addColumn('kpi_id', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('value', 'integer', [
                'default' => null,
                'limit' => 7,
                'null' => false,
            ])
            ->addColumn('timestamp', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('current_timestamp', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
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

        $this->table('dashboard_kpi_value_logs')
            ->addColumn('kpi_value_id', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('kpi_id', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('value', 'integer', [
                'default' => '0',
                'limit' => 7,
                'null' => false,
            ])
            ->addColumn('request_id', 'string', [
                'default' => null,
                'limit' => 36,
                'null' => true,
            ])
            ->addColumn('timestamp', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'kpi_value_id',
                ]
            )
            ->create();

        $this->table('dashboard_kpi_values')
            ->addColumn('kpi_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('value', 'integer', [
                'default' => null,
                'limit' => 8,
                'null' => true,
            ])
            ->addColumn('type', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addIndex(
                [
                    'kpi_id',
                ]
            )
            ->create();

        $this->table('dashboard_kpis')
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('owner_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('dashboard_kpi_attribute_count', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('json', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('value', 'integer', [
                'default' => null,
                'limit' => 8,
                'null' => true,
            ])
            ->create();

        $this->table('dashboard_kpi_attributes')
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

        $this->table('dashboard_kpi_logs')
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

        $this->table('dashboard_kpi_value_logs')
            ->addForeignKey(
                'kpi_value_id',
                'dashboard_kpi_values',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('dashboard_kpi_values')
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

        $this->table('cron')
            ->addColumn('request_id', 'string', [
                'after' => 'status',
                'default' => null,
                'length' => 36,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('dashboard_kpi_attributes')
            ->dropForeignKey(
                'kpi_id'
            );

        $this->table('dashboard_kpi_logs')
            ->dropForeignKey(
                'kpi_id'
            );

        $this->table('dashboard_kpi_value_logs')
            ->dropForeignKey(
                'kpi_value_id'
            );

        $this->table('dashboard_kpi_values')
            ->dropForeignKey(
                'kpi_id'
            );

        $this->table('cron')
            ->removeColumn('request_id')
            ->update();

        $this->dropTable('dashboard_kpi_attributes');

        $this->dropTable('dashboard_kpi_logs');

        $this->dropTable('dashboard_kpi_value_logs');

        $this->dropTable('dashboard_kpi_values');

        $this->dropTable('dashboard_kpis');
    }
}

