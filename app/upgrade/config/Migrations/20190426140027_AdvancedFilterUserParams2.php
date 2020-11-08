<?php
use Phinx\Migration\AbstractMigration;

class AdvancedFilterUserParams2 extends AbstractMigration
{

    public function up()
    {

        $this->table('advanced_filter_user_params')
            ->addColumn('type', 'integer', [
                'after' => 'user_id',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('advanced_filter_user_params')
            ->removeColumn('type')
            ->update();
    }
}

