<?php
use Phinx\Migration\AbstractMigration;

class DashboardCalendarEventMigration extends AbstractMigration
{  

    public function up()
    {

        $this->table('dashboard_calendar_events')
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('start', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('end', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $data = [
            [
                'model' => 'DashboardCalendarEvent',
                'status' => '1'
            ],
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }

    public function down()
    {
        $this->dropTable('dashboard_calendar_events');
    }
}

