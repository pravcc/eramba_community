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
                'auth_users' => '1',
                'auth_users_id' => '1',
                'auth_awareness' => '1',
                'auth_awareness_id' => '1',
                'auth_policies' => '1',
                'auth_policies_id' => NULL,
                'modified' => '2017-04-11 13:41:26',
            ],
        ];

        $table = $this->table('ldap_connector_authentication');
        $table->insert($data)->save();
    }
}
