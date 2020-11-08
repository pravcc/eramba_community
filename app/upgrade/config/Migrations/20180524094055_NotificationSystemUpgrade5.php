<?php
use Phinx\Migration\AbstractMigration;

class NotificationSystemUpgrade5 extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_items')
            ->addColumn('feedback_message', 'text', [
                'after' => 'feedback',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('notification_system_items')
            ->removeColumn('feedback_message')
            ->update();
    }
}

