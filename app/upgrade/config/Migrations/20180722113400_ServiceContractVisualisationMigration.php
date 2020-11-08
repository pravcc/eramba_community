<?php
use Phinx\Migration\AbstractMigration;

class ServiceContractVisualisationMigration extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {
        $data = [
            [
                'model' => 'ServiceContract',
                'status' => $this->defaultStatus
            ]
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }
}
