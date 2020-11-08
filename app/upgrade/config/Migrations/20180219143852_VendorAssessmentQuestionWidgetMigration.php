<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentQuestionWidgetMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessment_questions')
            ->addColumn('widget_type', 'integer', [
                'after' => 'score',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('vendor_assessment_questions')
            ->removeColumn('widget_type')
            ->update();
    }
}

