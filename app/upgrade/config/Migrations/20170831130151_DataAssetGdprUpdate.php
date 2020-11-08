<?php
use Phinx\Migration\AbstractMigration;

class DataAssetGdprUpdate extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_gdpr_archiving_drivers')
            ->addColumn('data_asset_gdpr_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('archiving_driver', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_gdpr_id',
                ]
            )
            ->create();

        $this->table('data_asset_gdpr_archiving_drivers')
            ->addForeignKey(
                'data_asset_gdpr_id',
                'data_asset_gdpr',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_gdpr')
            ->addColumn('purpose', 'text', [
                'after' => 'data_asset_id',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('right_to_be_informed', 'text', [
                'after' => 'purpose',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('transfer_outside_eea', 'integer', [
                'after' => 'destination',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('right_to_object', 'text', [
                'after' => 'right_to_decision',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();

        $this->table('data_asset_settings')
            ->addColumn('driver_for_compliance', 'text', [
                'after' => 'gdpr_enabled',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('data_asset_gdpr_archiving_drivers')
            ->dropForeignKey(
                'data_asset_gdpr_id'
            );

        $this->table('data_asset_gdpr')
            ->removeColumn('purpose')
            ->removeColumn('right_to_be_informed')
            ->removeColumn('transfer_outside_eea')
            ->removeColumn('right_to_object')
            ->update();

        $this->table('data_asset_settings')
            ->removeColumn('driver_for_compliance')
            ->update();

        $this->dropTable('data_asset_gdpr_archiving_drivers');
    }
}

