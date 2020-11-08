<?php
use Phinx\Migration\AbstractMigration;

class AdvancedFilterSystemFilter extends AbstractMigration
{

    public function up()
    {

        $this->table('advanced_filters')
            ->addColumn('system_filter', 'integer', [
                'after' => 'log_result_data',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('slug', 'string', [
                'after' => 'name',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('advanced_filters')
            ->removeColumn('system_filter')
            ->update();
    }
}

