<?php
use Phinx\Migration\AbstractMigration;

class RemoveComplianceAnalysisFindingsTables extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Owner' => [
                    'Owner' => [
                        'joinTable' => 'compliance_analysis_findings_owners',
                        'foreignKey' => 'compliance_analysis_finding_id',
                        'associationForeignKey' => 'owner_id'
                    ]
                ],
                'Collaborator' => [
                    'Collaborator' => [
                        'joinTable' => 'compliance_analysis_findings_collaborators',
                        'foreignKey' => 'compliance_analysis_finding_id',
                        'associationForeignKey' => 'collaborator_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'ComplianceAnalysisFinding', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->dropTable('compliance_analysis_findings_collaborators');

        $this->dropTable('compliance_analysis_findings_owners');
    }

    public function down()
    {
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

        $this->migrateData('down');
    }
}

