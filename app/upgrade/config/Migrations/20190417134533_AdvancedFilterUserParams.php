<?php
use Phinx\Migration\AbstractMigration;

class AdvancedFilterUserParams extends AbstractMigration
{

    public function up()
    {

        $this->table('advanced_filter_user_params')
            ->addColumn('advanced_filter_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('param', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('value', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
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
                    'user_id',
                ]
            )
            ->create();

        $this->table('advanced_filter_user_params')
            ->addForeignKey(
                'advanced_filter_id',
                'advanced_filters',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
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
        $this->table('advanced_filter_user_params')
            ->dropForeignKey(
                'advanced_filter_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->dropTable('advanced_filter_user_params');
    }
}

