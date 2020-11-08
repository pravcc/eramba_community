<?php
use Phinx\Migration\AbstractMigration;

class DueDateSystemFiltersFixMigration extends AbstractMigration
{
    public function up()
    {
    	if (class_exists('App')) {
	        App::uses('AdvancedFiltersModule', 'AdvancedFilters.Lib');

	        $AdvancedFiltersModule = new AdvancedFiltersModule();

	        $AdvancedFiltersModule->dueDateFiltersFix();
	    }
    }
}
