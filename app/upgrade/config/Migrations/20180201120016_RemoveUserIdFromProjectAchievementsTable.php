<?php
use Phinx\Migration\AbstractMigration;

class RemoveUserIdFromProjectAchievementsTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            ClassRegistry::flush();

            $cacheGlobalConfig = Configure::read('Cache.disable');
            Configure::write('Cache.disable', true);
            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;
            
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'TaskOwner' => 'user_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'ProjectAchievement', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('project_achievements')
            ->dropForeignKey([], 'project_achievements_ibfk_1')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('project_achievements')
            ->removeColumn('user_id')
            ->update();
    }

    public function down()
    {

        $this->table('project_achievements')
            ->addColumn('user_id', 'integer', [
                'after' => 'id',
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

        $this->table('project_achievements')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'project_achievements_ibfk_1'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

