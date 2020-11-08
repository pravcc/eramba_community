<?php
use Phinx\Migration\AbstractMigration;

class AddSamlConnectorsTable extends AbstractMigration
{
    public function up()
    {
        $this->table('saml_connectors')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false
            ])
            ->addColumn('identity_provider', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('idp_certificate', 'text', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('remote_sign_in_url', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false
            ])
            ->addColumn('remote_sign_out_url', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false
            ])
            ->addColumn('email_field', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false
            ])
            ->addColumn('sign_saml_request', 'integer', [
                'default' => 0,
                'limit' => 1,
                'null' => false
            ])
            ->addColumn('sp_certificate', 'text', [
                'default' => null,
                'null' => false
            ])
            ->addColumn('sp_private_key', 'text', [
                'default' => null,
                'null' => false
            ])
            ->addColumn('validate_saml_request', 'integer', [
                'default' => 1,
                'limit' => 1,
                'null' => false
            ])
            ->addColumn('status', 'integer', [
                'default' => 1,
                'limit' => 1,
                'null' => false
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->_syncFilters();
    }

    public function down()
    {
        $this->dropTable('saml_connectors');
    }

    protected function _syncFilters()
    {
        if (class_exists('App')) {
            $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
            
            ClassRegistry::init('SamlConnector');
      
            $AdvancedFilter->syncDefaultIndex(null, [
                'SamlConnector'
            ]);
        }
    }
}
