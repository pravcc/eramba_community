<?php
use Phinx\Migration\AbstractMigration;

class LdapConnectorFieldLength extends AbstractMigration
{

    public function up()
    {

        $this->table('ldap_connectors')
            ->changeColumn('ldap_bind_dn', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->changeColumn('ldap_base_dn', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->changeColumn('ldap_auth_filter', 'string', [
                'default' => '(| (sn=%USERNAME%) )',
                'limit' => 255,
                'null' => true,
            ])
            ->changeColumn('ldap_groupmemberlist_filter', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('ldap_connectors')
            ->changeColumn('ldap_bind_dn', 'string', [
                'default' => '',
                'length' => 150,
                'null' => false,
            ])
            ->changeColumn('ldap_base_dn', 'string', [
                'default' => '',
                'length' => 150,
                'null' => false,
            ])
            ->changeColumn('ldap_auth_filter', 'string', [
                'default' => '(| (sn=%USERNAME%) )',
                'length' => 150,
                'null' => true,
            ])
            ->changeColumn('ldap_groupmemberlist_filter', 'string', [
                'default' => null,
                'length' => 150,
                'null' => true,
            ])
            ->update();
    }
}
