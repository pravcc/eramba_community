<?php
use Phinx\Migration\AbstractMigration;

class ChangeAdvancedFilterUserParamsColumns extends AbstractMigration
{
    public function up()
    {

        $this->table('advanced_filter_user_params')
            ->changeColumn('param', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->changeColumn('value', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('advanced_filter_user_params')
            ->changeColumn('param', 'string', [
                'default' => '',
                'length' => 128,
                'null' => false,
            ])
            ->changeColumn('value', 'string', [
                'default' => null,
                'length' => 128,
                'null' => true,
            ])
            ->update();
    }
}
