<?php
use Phinx\Migration\AbstractMigration;

class ComplianceManagementMaintenanceFailedStatusMigration extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ComplianceManagement', 'Model');

            $ComplianceManagement = ClassRegistry::init('ComplianceManagement');

            $ids = $ComplianceManagement->find('list', [
                'fields' => [
                    'ComplianceManagement.id', 'ComplianceManagement.id'
                ],
                'recursive' => -1
            ]);

            if (!empty($ids)) {
                $ComplianceManagement->triggerObjectStatus(['security_service_maintenances_last_not_passed', 'compliance_analysis_finding_expired'], $ids, ['trigger' => false]);
            }
        }
    }
}
