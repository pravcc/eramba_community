<?php
use Phinx\Migration\AbstractMigration;

class AssetsPolicyException extends AbstractMigration
{

    public function up()
    {

        $this->table('assets_policy_exceptions')
            ->addColumn('asset_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('policy_exception_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'asset_id',
                ]
            )
            ->addIndex(
                [
                    'policy_exception_id',
                ]
            )
            ->create();

        $this->table('assets_policy_exceptions')
            ->addForeignKey(
                'asset_id',
                'assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'policy_exception_id',
                'policy_exceptions',
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
        $this->table('assets_policy_exceptions')
            ->dropForeignKey(
                'asset_id'
            )
            ->dropForeignKey(
                'policy_exception_id'
            );

        $this->dropTable('assets_policy_exceptions');
    }
}

