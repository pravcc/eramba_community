<?php
use Phinx\Migration\AbstractMigration;

class ProjectStatusCompletedMigration extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');

            ClassRegistry::init('AdvancedFilters.AdvancedFilter')->updateAll([
                'name' => '"Closed Projects"',
                'description' => '"This is the list of closed projects"',
            ], [
                'AdvancedFilter.model' => 'Project',
                'AdvancedFilter.slug' => 'completed'
            ]);
        }
    }

    public function down()
    {
    }
}
