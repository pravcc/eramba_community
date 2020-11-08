<?php
use Phinx\Migration\AbstractMigration;

class AdvancedFiltersUserLimit extends AbstractMigration
{

    public function up()
    {

        $this->table('advanced_filter_user_settings')
            ->addColumn('limit', 'integer', [
                'after' => 'default_index',
                'default' => '10',
                'length' => 4,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('advanced_filter_user_settings')
            ->removeColumn('limit')
            ->update();
    }
}

