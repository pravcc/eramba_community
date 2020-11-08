<?php
use Phinx\Migration\AbstractMigration;

class MappingRelationsMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('mapping_relations')
            ->addColumn('left_model', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('left_foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('right_model', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('right_foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'left_foreign_key',
                ]
            )
            ->addIndex(
                [
                    'right_foreign_key',
                ]
            )
            ->create();
    }

    public function down()
    {

        $this->dropTable('mapping_relations');
    }
}

