<?php
use Phinx\Migration\AbstractMigration;

class NotificationSystemUpgrade7 extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_item_custom_roles')
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

        $this->table('notification_system_item_custom_roles')
            ->removeColumn('type')
            ->update();
    }
}

