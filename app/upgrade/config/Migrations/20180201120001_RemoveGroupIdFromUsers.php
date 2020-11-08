<?php
use Phinx\Migration\AbstractMigration;

class RemoveGroupIdFromUsers extends AbstractMigration
{
    protected function moveGroups($type)
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            ClassRegistry::flush();

            $cacheGlobalConfig = Configure::read('Cache.disable');
            Configure::write('Cache.disable', true);
            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            ClassRegistry::init('UsersGroup')->moveGroupsToNewDbTable($type);
        }
    }

    public function up()
    {
        $this->moveGroups('up');
        
        //
        // Drop group_id column
        $table = $this->table('users');
        // $table->dropForeignKey('group_id');
        // $table->removeColumn('group_id');
        $table->update();
        //
    }

    public function down()
    {
        $table = $this->table('users');
        // $table->addColumn('group_id', 'integer', [
        //     'default' => 10,
        //     'limit' => 11,
        //     'null' => false,
        // ]);
        // $table->addForeignKey('group_id', 'groups', 'id', ['delete' => 'NO_ACTION', 'update' => 'CASCADE']);
        $table->update();

        $this->moveGroups('down');
    }
}
