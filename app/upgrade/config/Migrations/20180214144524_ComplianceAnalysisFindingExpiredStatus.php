<?php
use Phinx\Migration\AbstractMigration;

class ComplianceAnalysisFindingExpiredStatus extends AbstractMigration
{

    public function up()
    {

        $this->table('compliance_analysis_findings')
            ->addColumn('expired', 'integer', [
                'after' => 'due_date',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('compliance_analysis_findings')
            ->removeColumn('expired')
            ->update();
    }
}

