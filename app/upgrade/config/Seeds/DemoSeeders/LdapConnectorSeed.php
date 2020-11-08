<?php
use Phinx\Seed\AbstractSeed;

/**
 * LdapConnector seed.
 */
class LdapConnectorSeed extends AbstractSeed
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
                'name' => 'LDAP Authenticator Connector',
                'description' => '',
                'host' => 'ad.eramba.org',
                'domain' => 'eramba.org',
                'port' => '389',
                'ldap_bind_dn' => 'CN=Joe Ramone,OU=People,DC=corp,DC=eramba,DC=org',
                'ldap_bind_pw' => 'Br4t1sl4v4!',
                'ldap_base_dn' => 'DC=corp,DC=eramba,DC=org',
                'type' => 'authenticator',
                'ldap_auth_filter' => '(&(objectcategory=user)(sAMAccountName=%USERNAME%))',
                'ldap_auth_attribute' => 'sAMAccountName',
                'ldap_name_attribute' => 'displayName',
                'ldap_email_attribute' => 'mail',
                'ldap_memberof_attribute' => 'memberOf',
                'ldap_grouplist_filter' => '',
                'ldap_grouplist_name' => '',
                'ldap_groupmemberlist_filter' => '',
                'ldap_group_account_attribute' => '',
                'ldap_group_fetch_email_type' => 'email-attribute',
                'ldap_group_email_attribute' => '',
                'ldap_group_mail_domain' => '',
                'status' => '1',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 14:50:27',
                'modified' => '2017-04-10 14:50:27',
            ],
            [
                'id' => '2',
                'name' => 'LDAP Group Connector',
                'description' => '',
                'host' => 'ad.eramba.org',
                'domain' => 'eramba.org',
                'port' => '389',
                'ldap_bind_dn' => 'CN=Joe Ramone,OU=People,DC=corp,DC=eramba,DC=org',
                'ldap_bind_pw' => 'Br4t1sl4v4!',
                'ldap_base_dn' => 'DC=corp,DC=eramba,DC=org',
                'type' => 'group',
                'ldap_auth_filter' => '(| (sn=%USERNAME%) )',
                'ldap_auth_attribute' => '',
                'ldap_name_attribute' => '',
                'ldap_email_attribute' => '',
                'ldap_memberof_attribute' => '',
                'ldap_grouplist_filter' => '(objectCategory=group)',
                'ldap_grouplist_name' => 'cn',
                'ldap_groupmemberlist_filter' => '(&(objectCategory=user)(memberOf=CN=%GROUP%,OU=Groups,DC=corp,DC=eramba,DC=org))',
                'ldap_group_account_attribute' => 'sAMAccountName',
                'ldap_group_fetch_email_type' => 'email-attribute',
                'ldap_group_email_attribute' => 'mail',
                'ldap_group_mail_domain' => '',
                'status' => '1',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 14:52:00',
                'modified' => '2017-04-10 14:52:00',
            ],
        ];

        $table = $this->table('ldap_connectors');
        $table->insert($data)->save();
    }
}
