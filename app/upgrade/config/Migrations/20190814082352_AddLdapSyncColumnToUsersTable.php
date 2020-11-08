<?php
use Phinx\Migration\AbstractMigration;

class AddLdapSyncColumnToUsersTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('ldap_sync', 'integer', [
            'after' => 'account_ready',
            'default' => 0,
            'limit' => 1,
            'null' => false
        ]);
        $table->update();

        $this->query("UPDATE `users` SET `ldap_sync`='1' WHERE `users`.`ldap_synchronization_id` IS NOT NULL");
    }

    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('ldap_sync');
        $table->update();
    }
}
