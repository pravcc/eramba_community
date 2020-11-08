<?php
use Phinx\Migration\AbstractMigration;

class VisualisationOrder extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {

        $this->table('visualisation_settings')
            ->addColumn('order', 'integer', [
                'after' => 'status',
                'default' => '999',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->query("DELETE FROM `visualisation_settings` WHERE 1=1");

        // updated data
        $data = [
            [
                'id' => '1',
                'model' => 'Asset',
                'status' => $this->defaultStatus,
                'order' => '1',
            ],
            [
                'id' => '2',
                'model' => 'AssetReview',
                'status' => $this->defaultStatus,
                'order' => '2',
            ],
            [
                'id' => '3',
                'model' => 'Risk',
                'status' => $this->defaultStatus,
                'order' => '10',
            ],
            [
                'id' => '4',
                'model' => 'ThirdPartyRisk',
                'status' => $this->defaultStatus,
                'order' => '17',
            ],
            [
                'id' => '5',
                'model' => 'BusinessContinuity',
                'status' => $this->defaultStatus,
                'order' => '3',
            ],
            [
                'id' => '6',
                'model' => 'RiskReview',
                'status' => $this->defaultStatus,
                'order' => '11',
            ],
            [
                'id' => '7',
                'model' => 'ThirdPartyRiskReview',
                'status' => $this->defaultStatus,
                'order' => '18',
            ],
            [
                'id' => '8',
                'model' => 'BusinessContinuityReview',
                'status' => $this->defaultStatus,
                'order' => '4',
            ],
            [
                'id' => '9',
                'model' => 'SecurityPolicy',
                'status' => $this->defaultStatus,
                'order' => '12',
            ],
            [
                'id' => '10',
                'model' => 'SecurityPolicyReview',
                'status' => $this->defaultStatus,
                'order' => '13',
            ],
            [
                'id' => '11',
                'model' => 'SecurityService',
                'status' => $this->defaultStatus,
                'order' => '14',
            ],
            [
                'id' => '12',
                'model' => 'SecurityServiceAudit',
                'status' => $this->defaultStatus,
                'order' => '15',
            ],
            [
                'id' => '13',
                'model' => 'SecurityServiceMaintenance',
                'status' => $this->defaultStatus,
                'order' => '16',
            ],
            [
                'id' => '14',
                'model' => 'ComplianceException',
                'status' => $this->defaultStatus,
                'order' => '8',
            ],
            [
                'id' => '16',
                'model' => 'ComplianceAudit',
                'status' => $this->defaultStatus,
                'order' => '6',
            ],
            [
                'id' => '17',
                'model' => 'ComplianceAnalysisFinding',
                'status' => $this->defaultStatus,
                'order' => '5',
            ],
            [
                'id' => '18',
                'model' => 'ComplianceAuditSetting',
                'status' => $this->defaultStatus,
                'order' => '7',
            ],
            [
                'id' => '19',
                'model' => 'ComplianceFinding',
                'status' => $this->defaultStatus,
                'order' => '9',
            ],
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }

    public function down()
    {

        $this->table('visualisation_settings')
            ->removeColumn('order')
            ->update();
    }
}

