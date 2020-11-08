<?php
use Phinx\Migration\AbstractMigration;

class BusinessContinuityTaskSoftDeleteMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('business_continuity_tasks')
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
                'model' => 'BusinessContinuityTask',
                'status' => 1
            ]
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }

    public function down()
    {
        $this->table('business_continuity_tasks')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

