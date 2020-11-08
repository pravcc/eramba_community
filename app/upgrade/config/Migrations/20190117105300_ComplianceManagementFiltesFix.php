<?php
use Phinx\Migration\AbstractMigration;

class ComplianceManagementFiltesFix extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            $ret = true;

            $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');

            // delete compliance default auto-created filters thats missing a slug value
            // that was a bug that wasnt handled in new template
            $ret &= $AdvancedFilter->deleteAll([
                'AdvancedFilter.system_filter' => 1,
                'AdvancedFilter.model' => [
                    'ComplianceManagement',
                    'CompliancePackage',
                    'CompliancePackageItem'
                ]
            ]);

            // sync new compliance filters having slug
            $ret &= $AdvancedFilter->syncDefaultIndex(null, [
                'ComplianceManagement',
                'CompliancePackage'
            ]);

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $log = "Error occured when processing database synchronization for compliance managements filter fix.";
                CakeLog::write('error', "{$log} \n" . print_r($status, true));

                throw new Exception($log, 1);
                return false;
            }
        }
    }

    public function down()
    {
    }
}
