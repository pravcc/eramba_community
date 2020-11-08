<?php
use Phinx\Migration\AbstractMigration;

class AddSamlConnectorIdColumn extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('ldap_connector_authentication');
        $table->addColumn('auth_saml', 'integer', [
        	'after' => 'oauth_google_id',
        	'default' => null,
        	'limit' => 1,
        	'null' => false
        ])->addColumn('saml_connector_id', 'integer', [
            'after' => 'auth_saml',
            'default' => null,
            'limit' => 11,
            'null' => true,
        ])->addForeignKey(
            'saml_connector_id',
            'saml_connectors',
            'id',
            [
                'update' => 'SET NULL',
                'delete' => 'SET NULL'
            ]
        );

        $table->update();
    }

    public function down()
    {
    	$this->table('ldap_connector_authentication')
            ->dropForeignKey(
                'saml_connector_id'
            );

        $this->table('ldap_connector_authentication')
            ->removeColumn('auth_saml')
            ->removeColumn('saml_connector_id')
            ->update();
    }
}
