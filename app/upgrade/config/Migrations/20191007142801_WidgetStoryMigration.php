<?php
use Phinx\Migration\AbstractMigration;

class WidgetStoryMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('comments')
            ->changeColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('comments')
            ->addColumn('type', 'integer', [
                'after' => 'id',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('hash', 'string', [
                'after' => 'type',
                'default' => '',
                'length' => 255,
                'null' => false,
            ])
            ->update();


        if (class_exists('App')) {
            ClassRegistry::init('Setting')->deleteCache('');
            ClassRegistry::init('Setting')->syncAcl();

            $groupId = ClassRegistry::init('Group')->field('id', ['slug' => 'USER_MANAGEMENT']);

            $Permission = ClassRegistry::init(['class' => 'Permission', 'alias' => 'Permission']);

            $aro = [
                'model' => 'Group',
                'foreign_key' => $groupId
            ];

            $permissions = [
                'controllers/Widget/Widget/story',
            ];

            $ret = true;

            foreach ($permissions as $aco) {
                $ret &= $r = $Permission->allow($aro, $aco, '*', 1);
                if (!$r) {
                    CakeLog::write('debug', "Node ACL cannot be configured: {$aco}");
                }
            }

            if (!$ret) {
                CakeLog::write('debug', "Error occured when processing ACL Sync for User Management group.");
            }
        }
    }

    public function down()
    {

        $this->table('comments')
            ->changeColumn('foreign_key', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->removeColumn('type')
            ->removeColumn('hash')
            ->update();
    }
}

