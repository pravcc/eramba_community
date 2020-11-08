<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentFeedbackVisualisationMigration extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {
        $data = [
            [
                'model' => 'VendorAssessmentFeedback',
                'status' => $this->defaultStatus
            ],
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }
}
