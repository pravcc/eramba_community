<?php
use Phinx\Migration\AbstractMigration;

class DataAssetObjectStatus extends AbstractMigration
{

    public function up()
    {
        $this->table('data_assets')
            ->dropForeignKey([], 'data_assets_ibfk_3')
            ->removeIndexByName('workflow_owner_id')
            ->update();

        $this->table('data_assets')
            ->removeColumn('workflow_owner_id')
            ->removeColumn('workflow_status')
            ->update();

        $this->table('data_assets')
            ->addColumn('incomplete_analysis', 'integer', [
                'after' => 'order',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('incomplete_gdpr_analysis', 'integer', [
                'after' => 'incomplete_analysis',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('data_assets')
            ->addColumn('workflow_owner_id', 'integer', [
                'after' => 'data_asset_status_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('workflow_status', 'integer', [
                'after' => 'workflow_owner_id',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->removeColumn('incomplete_analysis')
            ->removeColumn('incomplete_gdpr_analysis')
            ->addIndex(
                [
                    'workflow_owner_id',
                ],
                [
                    'name' => 'workflow_owner_id',
                ]
            )
            ->update();

        $this->table('data_assets')
            ->addForeignKey(
                'workflow_owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }
}

