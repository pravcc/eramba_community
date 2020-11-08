<?php
use Phinx\Migration\AbstractMigration;

class NotificationSystemUpgrade2 extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_items')
            ->removeColumn('email_notification')
            ->removeColumn('header_notification')
            ->update();
    }

    public function down()
    {

        $this->table('notification_system_items')
            ->addColumn('email_notification', 'integer', [
                'after' => 'filename',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->addColumn('header_notification', 'integer', [
                'after' => 'email_notification',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->update();
    }
}

