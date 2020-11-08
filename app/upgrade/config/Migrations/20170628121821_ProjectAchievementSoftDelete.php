<?php
use Phinx\Migration\AbstractMigration;

class ProjectAchievementSoftDelete extends AbstractMigration
{

    public function up()
    {

        $this->table('project_achievements')
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

        $this->table('project_achievements')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

