<?php
use Phinx\Migration\AbstractMigration;

class MappingRelationsMigration2 extends AbstractMigration
{

    public function up()
    {

        $this->table('mapping_relations')
            ->addColumn('created', 'datetime', [
                'after' => 'right_foreign_key',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'after' => 'created',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();

        if (class_exists('App') && AppModule::loaded('Mapping')) {
            App::uses('ClassRegistry', 'Utility');

            $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
                
            AppModule::load('Translations');
            
            ClassRegistry::init('Mapping.ComplianceManagementMappingRelation');
      
            $AdvancedFilter->syncDefaultIndex(null, [
                'ComplianceManagementMappingRelation'
            ]);
        }
    }

    public function down()
    {

        $this->table('mapping_relations')
            ->removeColumn('created')
            ->removeColumn('modified')
            ->update();
    }
}

