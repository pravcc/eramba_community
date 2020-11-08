<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentFeedbackSoftDeleteMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessment_feedbacks')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('vendor_assessment_feedbacks')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

