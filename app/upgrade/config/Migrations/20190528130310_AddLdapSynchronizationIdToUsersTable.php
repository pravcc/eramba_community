<?php
use Phinx\Migration\AbstractMigration;

class AddLdapSynchronizationIdToUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('ldap_synchronization_id', 'integer', [
            'after' => 'default_password',
            'default' => null,
            'limit' => 11,
            'null' => true,
        ])->addForeignKey(
            'ldap_synchronization_id',
            'ldap_synchronizations',
            'id',
            [
                'update' => 'SET NULL',
                'delete' => 'SET NULL'
            ]
        );

        $table->update();
    }
}
