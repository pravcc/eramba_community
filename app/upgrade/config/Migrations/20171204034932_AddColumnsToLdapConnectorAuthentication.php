<?php
use Phinx\Migration\AbstractMigration;

class AddColumnsToLdapConnectorAuthentication extends AbstractMigration
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
        $table = $this->table('ldap_connector_authentication');
        $table->addColumn('oauth_google', 'integer', [
            'after' => 'auth_users_id',
            'default' => null,
            'limit' => 1,
            'null' => false
        ]);
        $table->addColumn('oauth_google_id', 'integer', [
            'after' => 'oauth_google',
            'default' => null,
            'limit' => 11,
            'null' => false
        ]);
        $table->update();
    }
}
