<?php
use Phinx\Migration\AbstractMigration;

class EditedColumn extends AbstractMigration
{

    public $tableList = [
        'legals',
        'third_parties',
        'security_services',
        'security_service_audits',
        'security_service_maintenances',
        'security_policies',
        'reviews',
        'risks',
        'assets',
        'third_party_risks',
        'business_continuities',
        'compliance_managements',
        'business_units',
        'processes',
        'service_contracts',
        'risk_exceptions',
        'policy_exceptions',
        'compliance_exceptions',
        'projects',
        'project_achievements',
        'project_expenses',
        'issues',
        'program_scopes',
        'program_issues',
        'team_roles',
        'compliance_packages',
        'goals',
        'goal_audits',
        'compliance_analysis_findings',
        'security_incidents',
        'security_incident_stages_security_incidents',
        'business_continuity_plans',
        'business_continuity_plan_audits',
        'business_continuity_tasks',
        'users',
        'system_logs',
        'groups',
        'oauth_connectors',
        'ldap_connectors',
        'data_assets',
        'data_asset_instances',
        'awareness_programs',
        'vendor_assessments',
        'vendor_assessment_feedbacks',
        'vendor_assessment_findings',
        'account_reviews',
        'account_review_feedbacks',
        'account_review_findings',
        'account_review_pulls'
    ];

    public function up()
    {
        foreach ($this->tableList as $table) {
            $this->table($table)
                ->addColumn('edited', 'datetime', [
                    'after' => 'modified',
                    'default' => null,
                    'length' => null,
                    'null' => true,
                ])
                ->update();

            ///$this->query("UPDATE `{$table}` SET `edited`=`modified` WHERE `{$table}`.`modified`!=`{$table}`.`created`");
        }
    }

    public function down()
    {
        foreach ($this->tableList as $table) {
            $this->table($table)
                ->removeColumn('edited')
                ->update();
        }
    }
}

