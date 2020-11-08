<?php
use Phinx\Migration\AbstractMigration;

class BusinessContinuityPlanAuditSoftDeleteMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('business_continuity_plan_audits')
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

        $data = [
            [
                'model' => 'BusinessContinuityPlanAudit',
                'status' => 1
            ]
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }

    public function down()
    {
        $this->table('business_continuity_plan_audits')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

