<?php
use Phinx\Migration\AbstractMigration;

class CustomFieldsMigration extends AbstractMigration
{
    public function up()
    {
        // insert custom field settings
        $rows = [
            [
                'model'  => 'ProgramIssue',
                'status' => 0
            ],
            [
                'model'  => 'Goal',
                'status' => 0
            ],
            [
                'model'  => 'TeamRole',
                'status' => 0
            ],
            [
                'model'  => 'Legal',
                'status' => 0
            ],
            [
                'model'  => 'SecurityPolicy',
                'status' => 0
            ],
            [
                'model'  => 'ComplianceManagement',
                'status' => 0
            ],
            [
                'model'  => 'Project',
                'status' => 0
            ],
        ];

        $table = $this->table('custom_field_settings');
        $table->insert($rows);
        $table->saveData();
    }

    public function down()
    {
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'ProgramIssue')");
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'Goal')");
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'TeamRole')");
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'Legal')");
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'SecurityPolicy')");
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'ComplianceManagement')");
        $this->query("DELETE FROM `custom_field_settings` WHERE (`model` = 'Project')");
    }
}
