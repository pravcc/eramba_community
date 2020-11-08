<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Config\ConfigInterface;
use Phinx\Console\PhinxApplication;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\StreamOutput;
use Phinx\Config\Config;
use Phinx\Console\Command\SeedRun;
use Symfony\Component\Console\Command\Command;

class VisualisationMigrationRls45 extends AbstractMigration
{
    public $defaultStatus = '1';

    public function up()
    {
        $data = [
            [
                'model' => 'BusinessUnit',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'AwarenessProgram',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'BusinessContinuityPlan',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'BusinessContinuityPlanAudit',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'DataAssetInstance',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'DataAsset',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'Goal',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'Legal',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'PolicyException',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'Process',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'ProgramIssue',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'ProgramScope',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'Project',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'ProjectAchievement',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'ProjectExpense',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'RiskException',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'SecurityIncident',
                'status' => $this->defaultStatus
            ],
            [
                'model' => 'ThirdParty',
                'status' => $this->defaultStatus
            ],
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }
}