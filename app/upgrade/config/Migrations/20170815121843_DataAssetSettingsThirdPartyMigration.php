<?php
use Phinx\Migration\AbstractMigration;

class DataAssetSettingsThirdPartyMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_settings_third_parties')
            ->addColumn('type', 'string', [
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('data_asset_setting_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('third_party_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_setting_id',
                ]
            )
            ->addIndex(
                [
                    'third_party_id',
                ]
            )
            ->create();

        $this->table('data_asset_settings_third_parties')
            ->addForeignKey(
                'data_asset_setting_id',
                'data_asset_settings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'third_party_id',
                'third_parties',
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
        $this->table('data_asset_settings_third_parties')
            ->dropForeignKey(
                'data_asset_setting_id'
            )
            ->dropForeignKey(
                'third_party_id'
            );

        $this->dropTable('data_asset_settings_third_parties');
    }
}

