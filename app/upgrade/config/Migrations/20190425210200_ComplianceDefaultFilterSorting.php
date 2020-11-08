<?php
use Phinx\Migration\AbstractMigration;

class ComplianceDefaultFilterSorting extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');

            ClassRegistry::init('AdvancedFilters.AdvancedFilterValue')->updateAll([
                'AdvancedFilterValue.value' => '"id"'
            ], [
                'AdvancedFilterValue.field' => '_order_column',
                'AdvancedFilter.system_filter' => '1',
                'OR' => [
                    [
                        'AdvancedFilter.model' => 'ComplianceManagement'
                    ],
                    [
                        'AdvancedFilter.model' => 'CompliancePackage'
                    ],
                    [
                        'AdvancedFilter.model' => 'CompliancePackageItem'
                    ]
                ]
            ]);
        }
    }

    public function down()
    {
    }
}

