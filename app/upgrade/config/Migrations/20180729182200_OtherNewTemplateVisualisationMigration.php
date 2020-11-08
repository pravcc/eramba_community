<?php
use Phinx\Migration\AbstractMigration;

class OtherNewTemplateVisualisationMigration extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {
        $data = [
            [
                'model' => 'TeamRole',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'SecurityServiceIssue',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'CompliancePackage',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'DataAssetSetting',
                'status' => $this->defaultStatus
            ]
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }
}
