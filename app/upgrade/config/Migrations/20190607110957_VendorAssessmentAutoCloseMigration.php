<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentAutoCloseMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessments')
            ->addColumn('auto_close', 'integer', [
                'after' => 'end_date',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('vendor_assessments')
            ->removeColumn('auto_close')
            ->update();
    }
}

