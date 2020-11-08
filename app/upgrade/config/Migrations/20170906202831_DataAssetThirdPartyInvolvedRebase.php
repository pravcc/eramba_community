<?php
use Phinx\Migration\AbstractMigration;

class DataAssetThirdPartyInvolvedRebase extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_settings')
            ->removeColumn('third_party_involved_all')
            ->update();

        $this->table('countries')
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('country_id', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addIndex(
                [
                    'foreign_key',
                ]
            )
            ->create();

        $this->table('data_asset_gdpr')
            ->addColumn('third_party_involved_all', 'integer', [
                'after' => 'transfer_outside_eea',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->dropTable('data_asset_settings_countries');
    }

    public function down()
    {

        $this->table('data_asset_settings_countries')
            ->addColumn('type', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('data_asset_setting_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('country_id', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_setting_id',
                ]
            )
            ->create();

        $this->table('data_asset_settings_countries')
            ->addForeignKey(
                'data_asset_setting_id',
                'data_asset_settings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_gdpr')
            ->removeColumn('third_party_involved_all')
            ->update();

        $this->table('data_asset_settings')
            ->addColumn('third_party_involved_all', 'integer', [
                'after' => 'driver_for_compliance',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->dropTable('countries');
    }
}

