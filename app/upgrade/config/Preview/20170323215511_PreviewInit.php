<?php
use Migrations\AbstractMigration;

class PreviewInit extends AbstractMigration
{
    // migration is missing a new row for custom_field_settings
    public function up()
    {

        $this->table('preview_another_section_items')
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('preview_another_section_items_section_items')
            ->addColumn('another_section_item_id', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('section_item_id', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'another_section_item_id',
                ]
            )
            ->addIndex(
                [
                    'section_item_id',
                ]
            )
            ->create();

        $this->table('preview_section_items')
            ->addColumn('varchar', 'string', [
                'default' => '',
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('text', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('date', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('tinyint_status', 'integer', [
                'default' => '1',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('toggle_status', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => true,
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
            ->addColumn('deleted', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('preview_section_items_collaborators')
            ->addColumn('section_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('collaborator_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'section_item_id',
                ]
            )
            ->create();

        $this->table('preview_sub_section_items')
            ->addColumn('section_item_id', 'integer', [
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
                    'section_item_id',
                ]
            )
            ->create();

        $this->table('preview_another_section_items_section_items')
            ->addForeignKey(
                'another_section_item_id',
                'preview_another_section_items',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'section_item_id',
                'preview_section_items',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('preview_section_items_collaborators')
            ->addForeignKey(
                'section_item_id',
                'preview_section_items',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('preview_sub_section_items')
            ->addForeignKey(
                'section_item_id',
                'preview_section_items',
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
        $this->table('preview_another_section_items_section_items')
            ->dropForeignKey(
                'another_section_item_id'
            )
            ->dropForeignKey(
                'section_item_id'
            );

        $this->table('preview_section_items_collaborators')
            ->dropForeignKey(
                'section_item_id'
            );

        $this->table('preview_sub_section_items')
            ->dropForeignKey(
                'section_item_id'
            );

        $this->dropTable('preview_another_section_items');
        $this->dropTable('preview_another_section_items_section_items');
        $this->dropTable('preview_section_items');
        $this->dropTable('preview_section_items_collaborators');
        $this->dropTable('preview_sub_section_items');
    }
}
