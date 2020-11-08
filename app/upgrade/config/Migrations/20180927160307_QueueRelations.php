<?php
use Phinx\Migration\AbstractMigration;

class QueueRelations extends AbstractMigration
{

    public function up()
    {

        $this->table('queue')
            ->addColumn('model', 'string', [
                'after' => 'queue_id',
                'default' => null,
                'length' => 128,
                'null' => true,
            ])
            ->addColumn('foreign_key', 'integer', [
                'after' => 'model',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('queue')
            ->removeColumn('model')
            ->removeColumn('foreign_key')
            ->update();
    }
}

