<?php
use Phinx\Migration\AbstractMigration;

class DataAssetSettingAllCountries extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_settings')
            ->addColumn('third_party_involved_all', 'integer', [
                'after' => 'gdpr_enabled',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('data_asset_settings')
            ->removeColumn('third_party_involved_all')
            ->update();
    }
}

