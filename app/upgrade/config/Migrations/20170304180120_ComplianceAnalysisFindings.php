<?php
use Phinx\Migration\AbstractMigration;

class ComplianceAnalysisFindings extends AbstractMigration
{

    public function up()
    {
        // $this->table('business_continuity_plan_audits')
            // ->dropForeignKey([], 'business_continuity_plan_audits_ibfk_2')
            // ->dropForeignKey([], 'business_continuity_plan_audits_ibfk_3')
            // ->update();
        // $this->table('security_service_maintenances')
        //     ->dropForeignKey([], 'security_service_maintenances_ibfk_2')
        //     ->update();
        $this->table('users')
            ->dropForeignKey([], 'users_ibfk_1')
            ->update();

        $this->table('compliance_analysis_findings')
            ->addColumn('title', 'string', [
                'default' => '',
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('due_date', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'integer', [
                'default' => '1',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('compliance_analysis_findings_collaborators')
            ->addColumn('compliance_analysis_finding_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('collaborator_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'collaborator_id',
                ]
            )
            ->addIndex(
                [
                    'compliance_analysis_finding_id',
                ]
            )
            ->create();

        $this->table('compliance_analysis_findings_compliance_managements')
            ->addColumn('compliance_analysis_finding_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('compliance_management_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'compliance_analysis_finding_id',
                ]
            )
            ->addIndex(
                [
                    'compliance_management_id',
                ]
            )
            ->create();

        $this->table('compliance_analysis_findings_owners')
            ->addColumn('compliance_analysis_finding_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('owner_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'compliance_analysis_finding_id',
                ]
            )
            ->addIndex(
                [
                    'owner_id',
                ]
            )
            ->create();

        $this->table('compliance_analysis_findings_collaborators')
            ->addForeignKey(
                'collaborator_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'compliance_analysis_finding_id',
                'compliance_analysis_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('compliance_analysis_findings_compliance_managements')
            ->addForeignKey(
                'compliance_analysis_finding_id',
                'compliance_analysis_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'compliance_management_id',
                'compliance_managements',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('compliance_analysis_findings_owners')
            ->addForeignKey(
                'compliance_analysis_finding_id',
                'compliance_analysis_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        // $this->table('business_continuity_plan_audits')
        //     ->addForeignKey(
        //         'business_continuity_plan_id',
        //         'business_continuity_plans',
        //         'id',
        //         [
        //             'update' => 'CASCADE',
        //             'delete' => 'CASCADE'
        //         ]
        //     )
        //     ->addForeignKey(
        //         'user_id',
        //         'users',
        //         'id',
        //         [
        //             'update' => 'CASCADE',
        //             'delete' => 'RESTRICT'
        //         ]
        //     )
        //     ->update();

        // $this->table('security_service_maintenances')
        //     ->addForeignKey(
        //         'user_id',
        //         'users',
        //         'id',
        //         [
        //             'update' => 'CASCADE',
        //             'delete' => 'RESTRICT'
        //         ]
        //     )
        //     ->update();

        $this->table('users')
            ->addForeignKey(
                'group_id',
                'groups',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        // lets insert a row for Custom Fields to work properly
        $customFieldSetting = [
            'id'    => 11,
            'model'  => 'ComplianceAnalysisFinding',
            'status' => 0
        ];

        $table = $this->table('custom_field_settings');
        $table->insert($customFieldSetting);
        $table->saveData();
    }

    public function down()
    {
        $this->table('compliance_analysis_findings_collaborators')
            ->dropForeignKey(
                'collaborator_id'
            )
            ->dropForeignKey(
                'compliance_analysis_finding_id'
            );

        $this->table('compliance_analysis_findings_compliance_managements')
            ->dropForeignKey(
                'compliance_analysis_finding_id'
            )
            ->dropForeignKey(
                'compliance_management_id'
            );

        $this->table('compliance_analysis_findings_owners')
            ->dropForeignKey(
                'compliance_analysis_finding_id'
            )
            ->dropForeignKey(
                'owner_id'
            );

        // $this->table('business_continuity_plan_audits')
        //     ->dropForeignKey(
        //         'business_continuity_plan_id'
        //     )
        //     ->dropForeignKey(
        //         'user_id'
        //     );

        // $this->table('security_service_maintenances')
        //     ->dropForeignKey(
        //         'user_id'
        //     );

        $this->table('users')
            ->dropForeignKey(
                'group_id'
            );

        // $this->table('business_continuity_plan_audits')
        //     ->addForeignKey(
        //         'user_id',
        //         'users',
        //         'id',
        //         [
        //             'update' => 'CASCADE',
        //             'delete' => 'NO_ACTION'
        //         ]
        //     )
        //     ->addForeignKey(
        //         'business_continuity_plan_id',
        //         'business_continuity_plans',
        //         'id',
        //         [
        //             'update' => 'CASCADE',
        //             'delete' => 'CASCADE'
        //         ]
        //     )
        //     ->update();

        // $this->table('security_service_maintenances')
        //     ->addForeignKey(
        //         'user_id',
        //         'users',
        //         'id',
        //         [
        //             'update' => 'CASCADE',
        //             'delete' => 'NO_ACTION'
        //         ]
        //     )
        //     ->update();

        $this->table('users')
            ->addForeignKey(
                'group_id',
                'groups',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'NO_ACTION'
                ]
            )
            ->update();

        $this->dropTable('compliance_analysis_findings');

        $this->dropTable('compliance_analysis_findings_collaborators');

        $this->dropTable('compliance_analysis_findings_compliance_managements');

        $this->dropTable('compliance_analysis_findings_owners');
    }
}

