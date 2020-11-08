<?php
use Phinx\Migration\AbstractMigration;

class NotificationSystemUpgrade1 extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_items')
            ->changeColumn('automated', 'integer', [
                'default' => '1',
                'limit' => 1,
                'null' => false,
            ])
            ->changeColumn('email_customized', 'integer', [
                'default' => '1',
                'limit' => 1,
                'null' => false,
            ])
            ->update();

        $this->table('notification_system_item_emails')
            ->addColumn('type', 'integer', [
                'after' => 'id',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->table('notification_system_items')
            ->addColumn('description', 'text', [
                'after' => 'name',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('feedback_show_item', 'integer', [
                'after' => 'status_feedback',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('notification_system_item_emails')
            ->removeColumn('type')
            ->update();

        $this->table('notification_system_items')
            ->changeColumn('automated', 'integer', [
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->changeColumn('email_customized', 'integer', [
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->removeColumn('description')
            ->removeColumn('feedback_show_item')
            ->update();
    }
}

