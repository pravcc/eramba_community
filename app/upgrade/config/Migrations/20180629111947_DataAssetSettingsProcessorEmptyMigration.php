<?php
use Phinx\Migration\AbstractMigration;

class DataAssetSettingsProcessorEmptyMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_settings')
            ->addColumn('processor_empty', 'integer', [
                'after' => 'dpo_empty',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('controller_empty', 'integer', [
                'after' => 'processor_empty',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('data_asset_settings')
            ->removeColumn('processor_empty')
            ->removeColumn('controller_empty')
            ->update();
    }
}

