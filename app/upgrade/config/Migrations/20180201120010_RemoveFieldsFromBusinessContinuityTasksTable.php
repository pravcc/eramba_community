<?php
use Phinx\Migration\AbstractMigration;

class RemoveFieldsFromBusinessContinuityTasksTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'AwarenessRole' => 'awareness_role'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'BusinessContinuityTask', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('business_continuity_tasks')
            ->dropForeignKey('awareness_role')
            ->removeIndexByName('awareness_role')
            ->update();

        $this->table('business_continuity_tasks')
            ->removeColumn('awareness_role')
            ->update();
    }

    public function down()
    {

        $this->table('business_continuity_tasks')
            ->addColumn('awareness_role', 'integer', [
                'after' => 'who',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'awareness_role',
                ],
                [
                    'name' => 'awareness_role',
                ]
            )
            ->update();

        $this->table('business_continuity_tasks')
            ->addForeignKey(
                'awareness_role',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

