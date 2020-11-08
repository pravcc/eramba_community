<?php
use Phinx\Migration\AbstractMigration;

class SecurityIncidentsCustomFields extends AbstractMigration
{
    public $rowID = 16;

    public function up()
    {
        $this->insertData();
    }

    protected function insertData() {
        // insert custom field settings
        $rows = [
            'id'    => $this->rowID,
            'model'  => 'SecurityIncident',
            'status' => 0
        ];

        $table = $this->table('custom_field_settings');
        $table->insert($rows);
        $table->saveData();
    }

    public function down()
    {
        $this->query("DELETE FROM `custom_field_settings` WHERE (`id` = {$this->rowID})");
    }
}
