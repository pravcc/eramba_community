<?php
use Phinx\Migration\AbstractMigration;

class AttachmentFilenameMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('attachments')
            ->addColumn('name', 'text', [
                'after' => 'foreign_key',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->query("UPDATE `attachments` SET `name`= replace(`filename`, '/files/uploads/', '') WHERE 1");
    }

    public function down()
    {
        $this->table('attachments')
            ->removeColumn('name')
            ->update();
    }
}

