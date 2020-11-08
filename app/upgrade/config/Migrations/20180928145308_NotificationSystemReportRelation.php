<?php
use Phinx\Migration\AbstractMigration;

class NotificationSystemReportRelation extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_items')
            ->addColumn('report_id', 'integer', [
                'after' => 'advanced_filter_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'report_id',
                ],
                [
                    'name' => 'idx_report_id',
                ]
            )
            ->update();

        $this->table('notification_system_items')
            ->addForeignKey(
                'report_id',
                'reports',
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
        $this->table('notification_system_items')
            ->dropForeignKey(
                'report_id'
            );

        $this->table('notification_system_items')
            ->removeIndexByName('idx_report_id')
            ->update();

        $this->table('notification_system_items')
            ->removeColumn('report_id')
            ->update();
    }
}

