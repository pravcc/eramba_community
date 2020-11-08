<?php
use Phinx\Migration\AbstractMigration;

class ComplianceManagementVisualisationMigration extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {
        $data = [
            [
                'model' => 'ComplianceManagement',
                'status' => $this->defaultStatus
            ]
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }
}
