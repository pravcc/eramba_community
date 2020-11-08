<?php
use Phinx\Migration\AbstractMigration;

class ObjectStatusStatusGroup extends AbstractMigration
{

    public function up()
    {

        $this->table('object_status_object_statuses')
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('status_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'status_id',
                ]
            )
            ->addIndex(
                [
                    'foreign_key',
                ]
            )
            ->create();

        $this->table('object_status_statuses')
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('object_status_object_statuses')
            ->addForeignKey(
                'status_id',
                'object_status_statuses',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->dropTable('object_statuses');
    }

    public function down()
    {
        $this->table('object_status_object_statuses')
            ->dropForeignKey(
                'status_id'
            );

        $this->table('object_statuses')
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'foreign_key',
                ]
            )
            ->create();

        $this->dropTable('object_status_object_statuses');

        $this->dropTable('object_status_statuses');
    }
}

