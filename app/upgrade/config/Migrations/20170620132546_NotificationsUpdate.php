<?php
use Phinx\Migration\AbstractMigration;

class NotificationsUpdate extends AbstractMigration
{

    public function up()
    {

        $this->table('notification_system_item_custom_roles')
            ->changeColumn('custom_identifier', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();

        $this->table('notification_system_item_custom_roles')
            ->addColumn('migration_updated', 'integer', [
                'after' => 'custom_identifier',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('notification_system_item_custom_roles')
            ->changeColumn('custom_identifier', 'string', [
                'default' => null,
                'length' => 255,
                'null' => false,
            ])
            ->removeColumn('migration_updated')
            ->update();
    }
}

