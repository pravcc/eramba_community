<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentVisualisationMigration extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {
        $data = [
            [
                'model' => 'VendorAssessment',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'VendorAssessmentFinding',
                'status' => $this->defaultStatus
            ],
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }
}
