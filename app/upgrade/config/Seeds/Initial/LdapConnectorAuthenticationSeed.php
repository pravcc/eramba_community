<?php
use Phinx\Seed\AbstractSeed;

/**
 * LdapConnectorAuthentication seed.
 */
class LdapConnectorAuthenticationSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'auth_users' => '0',
                'auth_users_id' => NULL,
                'auth_awareness' => '0',
                'auth_awareness_id' => NULL,
                'auth_policies' => '0',
                'auth_policies_id' => NULL,
                'modified' => '2015-08-16 11:20:01',
            ],
        ];

        $table = $this->table('ldap_connector_authentication');
        $table->insert($data)->save();
    }
}
