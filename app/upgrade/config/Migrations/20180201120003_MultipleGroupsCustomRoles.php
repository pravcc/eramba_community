<?php
use Phinx\Migration\AbstractMigration;

class MultipleGroupsCustomRoles extends AbstractMigration
{

    public function up()
    {

        $this->table('custom_roles_groups')
            ->addColumn('group_id', 'integer', [
                'default' => '0',
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
                    'group_id',
                ]
            )
            ->create();

        $this->table('custom_roles_role_groups')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('custom_roles_role_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('group_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'custom_roles_role_id',
                ]
            )
            ->addIndex(
                [
                    'group_id',
                ]
            )
            ->create();

        $this->table('custom_roles_groups')
            ->addForeignKey(
                'group_id',
                'groups',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('custom_roles_role_groups')
            ->addForeignKey(
                'custom_roles_role_id',
                'custom_roles_roles',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'group_id',
                'groups',
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
        $this->table('custom_roles_groups')
            ->dropForeignKey(
                'group_id'
            );

        $this->table('custom_roles_role_groups')
            ->dropForeignKey(
                'custom_roles_role_id'
            )
            ->dropForeignKey(
                'group_id'
            );

        $this->dropTable('custom_roles_groups');

        $this->dropTable('custom_roles_role_groups');
    }
}

