<?php
use Phinx\Migration\AbstractMigration;

class ReportsMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('report_block_chart_settings')
            ->addColumn('report_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('report_block_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('chart_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('content', 'text', [
                'default' => '',
                'limit' => null,
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
                    'report_block_id',
                ]
            )
            ->addIndex(
                [
                    'report_id',
                ]
            )
            ->create();

        $this->table('report_block_filter_settings')
            ->addColumn('report_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('report_block_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('advanced_filter_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('content', 'text', [
                'default' => '',
                'limit' => null,
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
                    'advanced_filter_id',
                ]
            )
            ->addIndex(
                [
                    'report_block_id',
                ]
            )
            ->addIndex(
                [
                    'report_id',
                ]
            )
            ->create();

        $this->table('report_block_table_fields')
            ->addColumn('report_block_table_setting_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('field', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('order', 'integer', [
                'default' => '1',
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
                    'report_block_table_setting_id',
                ]
            )
            ->create();

        $this->table('report_block_table_settings')
            ->addColumn('report_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('report_block_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('content', 'text', [
                'default' => '',
                'limit' => null,
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
                    'report_block_id',
                ]
            )
            ->addIndex(
                [
                    'report_id',
                ]
            )
            ->create();

        $this->table('report_block_text_settings')
            ->addColumn('report_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('report_block_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('content', 'text', [
                'default' => null,
                'limit' => null,
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
                    'report_block_id',
                ]
            )
            ->addIndex(
                [
                    'report_id',
                ]
            )
            ->create();

        $this->table('report_blocks')
            ->addColumn('report_template_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('design', 'integer', [
                'default' => '1',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('size', 'integer', [
                'default' => '1',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('order', 'integer', [
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
                    'parent_id',
                ]
            )
            ->addIndex(
                [
                    'report_template_id',
                ]
            )
            ->create();

        $this->table('report_templates')
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('format', 'integer', [
                'default' => '1',
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
            ->create();

        $this->table('reports')
            ->addColumn('report_template_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('slug', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('protected', 'integer', [
                'default' => '0',
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
                    'report_template_id',
                ]
            )
            ->create();

        $this->table('report_block_chart_settings')
            ->addForeignKey(
                'report_block_id',
                'report_blocks',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'report_id',
                'reports',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('report_block_filter_settings')
            ->addForeignKey(
                'advanced_filter_id',
                'advanced_filters',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->addForeignKey(
                'report_block_id',
                'report_blocks',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'report_id',
                'reports',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('report_block_table_fields')
            ->addForeignKey(
                'report_block_table_setting_id',
                'report_block_table_settings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('report_block_table_settings')
            ->addForeignKey(
                'report_block_id',
                'report_blocks',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'report_id',
                'reports',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('report_block_text_settings')
            ->addForeignKey(
                'report_block_id',
                'report_blocks',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'report_id',
                'reports',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('report_blocks')
            ->addForeignKey(
                'parent_id',
                'report_blocks',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->addForeignKey(
                'report_template_id',
                'report_templates',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('reports')
            ->addForeignKey(
                'report_template_id',
                'report_templates',
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
        $this->table('report_block_chart_settings')
            ->dropForeignKey(
                'report_block_id'
            )
            ->dropForeignKey(
                'report_id'
            );

        $this->table('report_block_filter_settings')
            ->dropForeignKey(
                'advanced_filter_id'
            )
            ->dropForeignKey(
                'report_block_id'
            )
            ->dropForeignKey(
                'report_id'
            );

        $this->table('report_block_table_fields')
            ->dropForeignKey(
                'report_block_table_setting_id'
            );

        $this->table('report_block_table_settings')
            ->dropForeignKey(
                'report_block_id'
            )
            ->dropForeignKey(
                'report_id'
            );

        $this->table('report_block_text_settings')
            ->dropForeignKey(
                'report_block_id'
            )
            ->dropForeignKey(
                'report_id'
            );

        $this->table('report_blocks')
            ->dropForeignKey(
                'parent_id'
            )
            ->dropForeignKey(
                'report_template_id'
            );

        $this->table('reports')
            ->dropForeignKey(
                'report_template_id'
            );

        $this->dropTable('report_block_chart_settings');

        $this->dropTable('report_block_filter_settings');

        $this->dropTable('report_block_table_fields');

        $this->dropTable('report_block_table_settings');

        $this->dropTable('report_block_text_settings');

        $this->dropTable('report_blocks');

        $this->dropTable('report_templates');

        $this->dropTable('reports');
    }
}

