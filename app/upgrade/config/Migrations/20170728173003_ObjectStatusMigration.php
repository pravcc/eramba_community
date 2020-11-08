<?php
use Phinx\Migration\AbstractMigration;

class ObjectStatusMigration extends AbstractMigration
{

    public function up()
    {

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
    }

    public function down()
    {

        $this->dropTable('object_statuses');
    }
}

