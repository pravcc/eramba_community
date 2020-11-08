<?php
use Phinx\Migration\AbstractMigration;

class ComplianceFindingsRequirements extends AbstractMigration
{

    public function up()
    {

        $this->table('compliance_analysis_findings_compliance_package_items')
            ->addColumn('compliance_analysis_finding_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('compliance_package_item_id', 'integer', [
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
                    'compliance_package_item_id',
                ]
            )
            ->create();

        $this->table('compliance_analysis_findings_third_parties')
            ->addColumn('compliance_analysis_finding_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('third_party_id', 'integer', [
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
                    'third_party_id',
                ]
            )
            ->create();

        $this->table('compliance_analysis_findings_compliance_package_items')
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
                'compliance_package_item_id',
                'compliance_package_items',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('compliance_analysis_findings_third_parties')
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
                'third_party_id',
                'third_parties',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('compliance_analysis_findings_compliance_package_items')
            ->dropForeignKey(
                'compliance_analysis_finding_id'
            )
            ->dropForeignKey(
                'compliance_package_item_id'
            );

        $this->table('compliance_analysis_findings_third_parties')
            ->dropForeignKey(
                'compliance_analysis_finding_id'
            )
            ->dropForeignKey(
                'third_party_id'
            );

        $this->dropTable('compliance_analysis_findings_compliance_package_items');

        $this->dropTable('compliance_analysis_findings_third_parties');
    }
}

