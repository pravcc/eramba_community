<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentFindingsQuestionsMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessment_findings_questions')
            ->addColumn('vendor_assessment_finding_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_question_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'vendor_assessment_finding_id',
                ]
            )
            ->addIndex(
                [
                    'vendor_assessment_question_id',
                ]
            )
            ->create();

        $this->table('vendor_assessment_findings_questions')
            ->addForeignKey(
                'vendor_assessment_finding_id',
                'vendor_assessment_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'vendor_assessment_question_id',
                'vendor_assessment_questions',
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
        $this->table('vendor_assessment_findings_questions')
            ->dropForeignKey(
                'vendor_assessment_finding_id'
            )
            ->dropForeignKey(
                'vendor_assessment_question_id'
            );

        $this->dropTable('vendor_assessment_findings_questions');
    }
}

