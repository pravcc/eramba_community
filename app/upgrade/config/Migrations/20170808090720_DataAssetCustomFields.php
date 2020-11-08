<?php
use Phinx\Migration\AbstractMigration;

class DataAssetCustomFields extends AbstractMigration
{

    public function up()
    {
        $this->insertData();
    }

    protected function insertData() {
        // insert custom field settings
        $rows = [
            'model'  => 'DataAsset',
            'status' => 0
        ];

        $table = $this->table('custom_field_settings');
        $table->insert($rows);
        $table->saveData();
    }

    public function down()
    {
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'DataAsset')");
    }
}
