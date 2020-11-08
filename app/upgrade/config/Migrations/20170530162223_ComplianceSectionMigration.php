<?php
use Phinx\Migration\AbstractMigration;

class ComplianceSectionMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('compliance_analysis_findings')
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

        $this->table('compliance_exceptions')
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

        $this->table('compliance_analysis_findings')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('compliance_exceptions')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

