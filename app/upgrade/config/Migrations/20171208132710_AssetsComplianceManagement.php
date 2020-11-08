<?php
use Phinx\Migration\AbstractMigration;

class AssetsComplianceManagement extends AbstractMigration
{

    public function up()
    {

        $this->table('assets_compliance_managements')
            ->addColumn('asset_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('compliance_management_id', 'integer', [
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
                    'compliance_management_id',
                ]
            )
            ->create();

        $this->table('assets_compliance_managements')
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
                'compliance_management_id',
                'compliance_managements',
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
        $this->table('assets_compliance_managements')
            ->dropForeignKey(
                'asset_id'
            )
            ->dropForeignKey(
                'compliance_management_id'
            );

        $this->dropTable('assets_compliance_managements');
    }
}

