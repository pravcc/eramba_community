<?php
use Phinx\Migration\AbstractMigration;

class TmpAttachmentsMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('attachments')
            ->changeColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('attachments')
            ->addColumn('type', 'integer', [
                'after' => 'id',
                'default' => '1',
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('hash', 'string', [
                'after' => 'type',
                'default' => null,
                'length' => 255,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('attachments')
            ->changeColumn('foreign_key', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->removeColumn('type')
            ->removeColumn('hash')
            ->update();
    }
}

