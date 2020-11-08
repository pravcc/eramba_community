<?php

use Phinx\Migration\AbstractMigration;

class Initial extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $exists = $this->hasTable('users');
        if ($exists) {
            return;
        }

        $file = __DIR__ . DS . 'cake3_deploy.sql';
        if (!file_exists($file)) {
            throw new \Exception('Cannot find db dump');
        }
        $sql = file_get_contents($file);

        // $sql = file_get_contents('dump.sql');
        $this->query($sql);
    }
}
