<?php
use Phinx\Migration\AbstractMigration;

class ServiceContractCustomFieldsMigration extends AbstractMigration
{
    public function up()
    {
        // insert custom field settings
        $rows = [
            [
                'model'  => 'ServiceContract',
                'status' => 0
            ]
        ];

        $table = $this->table('custom_field_settings');
        $table->insert($rows);
        $table->saveData();
    }

    public function down()
    {
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'ServiceContract')");
    }
}
