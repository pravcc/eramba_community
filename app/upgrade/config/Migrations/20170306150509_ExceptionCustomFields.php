<?php
use Phinx\Migration\AbstractMigration;

class ExceptionCustomFields extends AbstractMigration
{

    public function up()
    {
        // insert custom field settings
        $rows = [
            [
                'id'    => 13,
                'model'  => 'RiskException',
                'status' => 0
            ],
            [
                'id'    => 14,
                'model'  => 'PolicyException',
                'status' => 0
            ],
            [
                'id'    => 15,
                'model'  => 'ComplianceException',
                'status' => 0
            ]
        ];

        $table = $this->table('custom_field_settings');
        $table->insert($rows);
        $table->saveData();

        $this->query("UPDATE `settings` SET `value`='e1.0.1.016' WHERE `settings`.`variable`='DB_SCHEMA_VERSION'");
    }
}
