<?php
use Phinx\Migration\AbstractMigration;

class NotificationSystemUpgrade6 extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_items')
            ->addColumn('feedback_completed_notification', 'integer', [
                'after' => 'feedback_show_item',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->table('notification_system_items_users')
            ->addColumn('type', 'integer', [
                'after' => 'id',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('notification_system_items')
            ->removeColumn('feedback_completed_notification')
            ->update();

        $this->table('notification_system_items_users')
            ->removeColumn('type')
            ->update();
    }
}

