<?php
use Phinx\Migration\AbstractMigration;

class WorkflowOwnerForeignKeyRemoval extends AbstractMigration
{

    public $tables = [
        'asset_classifications',
        'asset_labels',
        'assets',
        'business_continuities',
        'business_continuity_plans',
        'business_continuity_plan_audits',
        'business_continuity_tasks',
        'business_units',
        'compliance_managements',
        'compliance_package_items',
        'goal_audits',
        'legals',
        'policy_exceptions',
        'processes',
        'project_achievements',
        'project_expenses',
        'projects',
        'security_services',
        'risk_classifications',
        'risk_exceptions',
        'risks',
        'security_incidents',
        'security_policies',
        'security_service_audits',
        'security_service_maintenances',
        'service_classifications',
        'service_contracts',
        'third_parties',
        'third_party_risks',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            $table = $this->table($table);

            $table->dropForeignKey('workflow_owner_id')
                ->removeIndexByName('workflow_owner_id')
                ->update();
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            $table = $this->table($table);
            
            $table
                ->addIndex(
                    [
                        'workflow_owner_id',
                    ],
                    [
                        'name' => 'workflow_owner_id',
                    ]
                )
                ->addForeignKey(
                    'workflow_owner_id',
                    'users',
                    'id',
                    [
                        'update' => 'CASCADE',
                        'delete' => 'CASCADE'
                    ]
                )
                ->update();
        }
    }
}

