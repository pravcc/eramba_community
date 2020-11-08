<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentFindingCloseDate extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessment_findings')
            ->addColumn('close_date', 'date', [
                'after' => 'deadline',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('auto_close_date', 'integer', [
                'after' => 'close_date',
                'default' => '1',
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('vendor_assessment_findings')
            ->removeColumn('close_date')
            ->removeColumn('auto_close_date')
            ->update();
    }
}

