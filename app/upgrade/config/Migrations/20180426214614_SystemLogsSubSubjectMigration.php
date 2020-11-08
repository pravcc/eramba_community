<?php
use Phinx\Migration\AbstractMigration;

class SystemLogsSubSubjectMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('system_logs')
            ->addColumn('sub_model', 'string', [
                'after' => 'foreign_key',
                'default' => '',
                'length' => 255,
                'null' => true,
            ])
            ->addColumn('sub_foreign_key', 'integer', [
                'after' => 'sub_model',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('system_logs')
            ->removeColumn('sub_model')
            ->removeColumn('sub_foreign_key')
            ->update();
    }
}

