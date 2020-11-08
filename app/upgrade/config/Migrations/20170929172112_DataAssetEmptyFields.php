<?php
use Phinx\Migration\AbstractMigration;

class DataAssetEmptyFields extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_gdpr')
            ->addColumn('archiving_driver_empty', 'integer', [
                'after' => 'right_to_erasure',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->table('data_asset_settings')
            ->addColumn('dpo_empty', 'integer', [
                'after' => 'driver_for_compliance',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('controller_representative_empty', 'integer', [
                'after' => 'dpo_empty',
                'default' => '0',
                'length' => 3,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('data_asset_gdpr')
            ->removeColumn('archiving_driver_empty')
            ->update();

        $this->table('data_asset_settings')
            ->removeColumn('dpo_empty')
            ->removeColumn('controller_representative_empty')
            ->update();
    }
}

