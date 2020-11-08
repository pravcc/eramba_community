<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentFeedbackBlockMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessment_feedbacks')
            ->addColumn('locked', 'integer', [
                'after' => 'completed',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('vendor_assessment_feedbacks')
            ->removeColumn('locked')
            ->update();
    }
}

