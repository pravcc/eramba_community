<?php
use Phinx\Migration\AbstractMigration;

class VisualisationUpdate extends AbstractMigration
{

    public function up()
    {

        $this->table('custom_roles_role_users')
            ->removeColumn('field')
            ->update();
        $this->table('custom_roles_roles')
            ->removeIndexByName('role')
            ->update();

        $this->table('custom_roles_roles')
            ->removeColumn('role')
            ->update();

        $this->table('custom_roles_roles')
            ->addColumn('field', 'string', [
                'after' => 'model',
                'default' => '',
                'length' => 155,
                'null' => false,
            ])
            ->addIndex(
                [
                    'model',
                ],
                [
                    'name' => 'idx_model',
                ]
            )
            ->update();
    }

    public function down()
    {

        $this->table('custom_roles_role_users')
            ->addColumn('field', 'string', [
                'after' => 'foreign_key',
                'default' => '',
                'length' => 155,
                'null' => false,
            ])
            ->update();

        $this->table('custom_roles_roles')
            ->removeIndexByName('idx_model')
            ->update();

        $this->table('custom_roles_roles')
            ->addColumn('role', 'string', [
                'after' => 'model',
                'default' => '',
                'length' => 155,
                'null' => false,
            ])
            ->removeColumn('field')
            ->addIndex(
                [
                    'role',
                ],
                [
                    'name' => 'role',
                ]
            )
            ->update();
    }
}

