<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentFeedbackCompletedMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessment_feedbacks')
            ->addColumn('completed', 'integer', [
                'after' => 'answer',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('vendor_assessment_feedbacks')
            ->removeColumn('completed')
            ->update();
    }
}

