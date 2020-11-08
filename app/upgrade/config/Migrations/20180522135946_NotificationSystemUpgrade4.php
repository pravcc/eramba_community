<?php
use Phinx\Migration\AbstractMigration;

class NotificationSystemUpgrade4 extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_items_users')
            ->removeColumn('created')
            ->update();

        $this->table('notification_system_item_logs')
            ->addColumn('hash', 'text', [
                'after' => 'notification_system_item_object_id',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('notification_system_items')
            ->addColumn('email_type', 'integer', [
                'after' => 'email_body',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('notification_system_item_logs')
            ->removeColumn('hash')
            ->update();

        $this->table('notification_system_items')
            ->removeColumn('email_type')
            ->update();

        $this->table('notification_system_items_users')
            ->addColumn('created', 'datetime', [
                'after' => 'user_id',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }
}

