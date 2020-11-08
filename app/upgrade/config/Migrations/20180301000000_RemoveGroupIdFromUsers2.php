<?php
use Phinx\Migration\AbstractMigration;

class RemoveGroupIdFromUsers2 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->dropForeignKey('group_id');
        $table->removeColumn('group_id');
        $table->update();
    }

    public function down()
    {
        $table = $this->table('users');
        $table->addColumn('group_id', 'integer', [
            'default' => 10,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addForeignKey('group_id', 'groups', 'id', ['delete' => 'NO_ACTION', 'update' => 'CASCADE']);
        $table->update();
    }
}
