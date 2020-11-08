<?php
use Phinx\Migration\AbstractMigration;

class RemoveForeignKeyLdapSyncIdFromUsersTable extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->dropForeignKey(
                'ldap_synchronization_id'
            );
    }

    public function down()
    {

    }
}
