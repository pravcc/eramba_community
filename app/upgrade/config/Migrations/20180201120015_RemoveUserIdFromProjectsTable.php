<?php
use Phinx\Migration\AbstractMigration;

class RemoveUserIdFromProjectsTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            ClassRegistry::init('Setting')->deleteCache(null);
            
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Owner' => 'user_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'Project', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');
        $this->table('projects')
            ->dropForeignKey([], 'projects_ibfk_2')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('projects')
            ->removeColumn('user_id')
            ->update();
    }

    public function down()
    {
        $this->table('projects')
            ->addColumn('user_id', 'integer', [
                'after' => 'project_status_id',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'user_id',
                ]
            )
            ->update();

        $this->table('projects')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'projects_ibfk_2'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

