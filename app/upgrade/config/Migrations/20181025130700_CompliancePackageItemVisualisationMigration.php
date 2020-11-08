<?php
use Phinx\Migration\AbstractMigration;

class CompliancePackageItemVisualisationMigration extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {
        $data = [
            [
                'model' => 'CompliancePackageItem',
                'status' => $this->defaultStatus
            ]
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }
}
