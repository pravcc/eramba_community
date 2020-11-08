<?php
use Phinx\Migration\AbstractMigration;

class ThirdPartyAuditsPortal extends AbstractMigration
{

    public function up()
    {

        $this->table('ldap_connector_authentication')
            ->addColumn('auth_compliance_audit', 'integer', [
                'after' => 'auth_policies_id',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('ldap_connector_authentication')
            ->removeColumn('auth_compliance_audit')
            ->update();
    }
}

