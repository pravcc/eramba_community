<?php
use Phinx\Migration\AbstractMigration;

class DataAssetInstanceNewObjecStatus extends AbstractMigration
{

    public function up()
    {

        $this->table('data_asset_settings')
            ->removeColumn('description')
            ->update();

        $this->table('data_asset_instances')
            ->addColumn('controls_with_missing_audits', 'integer', [
                'after' => 'controls_with_failed_audits',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('data_asset_instances')
            ->removeColumn('controls_with_missing_audits')
            ->update();

        $this->table('data_asset_settings')
            ->addColumn('description', 'text', [
                'after' => 'name',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }
}

