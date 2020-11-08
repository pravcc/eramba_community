<?php
use Phinx\Migration\AbstractMigration;

class AdvancedFiltersDescription extends AbstractMigration
{

    public function up()
    {

        $this->table('advanced_filters')
            ->addColumn('description', 'text', [
                'after' => 'name',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('advanced_filters')
            ->removeColumn('description')
            ->update();
    }
}

