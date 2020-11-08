<?php
use Phinx\Migration\AbstractMigration;

class NotificationsFeedbackReport extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_items')
            ->addColumn('feedback_report_id', 'integer', [
                'after' => 'feedback_show_item',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'feedback_report_id',
                ],
                [
                    'name' => 'idx_feedback_report_id',
                ]
            )
            ->update();

        $this->table('notification_system_items')
            ->addForeignKey(
                'feedback_report_id',
                'reports',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('notification_system_items')
            ->dropForeignKey(
                'feedback_report_id'
            );

        $this->table('notification_system_items')
            ->removeIndexByName('idx_feedback_report_id')
            ->update();

        $this->table('notification_system_items')
            ->removeColumn('feedback_report_id')
            ->update();
    }
}

