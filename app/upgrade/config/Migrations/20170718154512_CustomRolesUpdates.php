<?php
use Phinx\Migration\AbstractMigration;

class CustomRolesUpdates extends AbstractMigration
{

    public function up()
    {
        $this->table('custom_roles_users')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('custom_roles_users')
            ->addForeignKey(
                'user_id',
                'users',
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
        $this->table('custom_roles_users')
            ->dropForeignKey(
                'user_id'
            );

        $this->dropTable('custom_roles_users');
    }
}

