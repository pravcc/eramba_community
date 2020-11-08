<?php
use Phinx\Migration\AbstractMigration;

class DataAssetStagesRemoval extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_gdpr')
            ->removeColumn('right_to_restrict')
            ->removeColumn('right_to_object')
            ->update();
    }

    public function down()
    {

        $this->table('data_asset_gdpr')
            ->addColumn('right_to_restrict', 'text', [
                'after' => 'right_to_decision',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('right_to_object', 'text', [
                'after' => 'right_to_restrict',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }
}

