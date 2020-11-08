<?php
use Phinx\Migration\AbstractMigration;

class AddRecurrenceFieldsToSecurityServicesTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('security_services');
        $table->addColumn('audit_calendar_type', 'integer', [
            'after' => 'documentation_url',
            'default' => 0,
            'limit' => 3,
            'null' => false,
        ])->addColumn('audit_calendar_recurrence_start_date', 'datetime', [
            'after' => 'audit_calendar_type',
            'default' => null,
            'null' => false
        ])->addColumn('audit_calendar_recurrence_frequency', 'integer', [
            'after' => 'audit_calendar_recurrence_start_date',
            'default' => 0,
            'limit' => 11,
            'null' => false
        ])->addColumn('audit_calendar_recurrence_period', 'integer', [
            'after' => 'audit_calendar_recurrence_frequency',
            'default' => 0,
            'limit' => 11,
            'null' => false
        ])->addColumn('maintenance_calendar_type', 'integer', [
            'after' => 'audit_calendar_recurrence_period',
            'default' => 0,
            'limit' => 3,
            'null' => false
        ])->addColumn('maintenance_calendar_recurrence_start_date', 'datetime', [
            'after' => 'maintenance_calendar_type',
            'default' => null,
            'null' => false
        ])->addColumn('maintenance_calendar_recurrence_frequency', 'integer', [
            'after' => 'maintenance_calendar_recurrence_start_date',
            'default' => 0,
            'limit' => 11,
            'null' => false
        ])->addColumn('maintenance_calendar_recurrence_period', 'integer', [
            'after' => 'maintenance_calendar_recurrence_frequency',
            'default' => 0,
            'limit' => 11,
            'null' => false
        ]);

        $table->update();

        $this->setCalendarTypeBasedOnOldDates();
    }

    protected function setCalendarTypeBasedOnOldDates()
    {
        if (class_exists('App')) {
            App::uses('Hash', 'Utility');
            App::uses('SecurityService', 'Model');

            $SecurityService = ClassRegistry::init('SecurityService');

            $securityServices = $SecurityService->find('all', [
                'fields' => [
                    'id'
                ],
                'recursive' => -1
            ]);

            $auditDates = $SecurityService->SecurityServiceAuditDate->find('all', [
                'fields' => [
                    'id', 'security_service_id'
                ],
                'group' => [
                    'security_service_id'
                ],
                'recursive' => -1
            ]);

            $maintenanceDates = $SecurityService->SecurityServiceMaintenanceDate->find('all', [
                'fields' => [
                    'id', 'security_service_id'
                ],
                'group' => [
                    'security_service_id'
                ],
                'recursive' => -1
            ]);

            $secServIds = Hash::extract($securityServices, '{n}.SecurityService.id');
            $secServFromAuditDates = Hash::extract($auditDates, '{n}.SecurityServiceAuditDate.security_service_id');
            $secServFromMaintenanceDates = Hash::extract($maintenanceDates, '{n}.SecurityServiceMaintenanceDate.security_service_id');
            
            $ret = true;
            foreach ($secServIds as $id) {
                $exec = false;

                $auditCalendarType = SecurityService::CALENDAR_TYPE_NO_DATES;
                if (in_array($id, $secServFromAuditDates)) {
                    $auditCalendarType = SecurityService::CALENDAR_TYPE_SPECIFIC_DATES;
                    $exec = true;
                }

                $maintenanceCalendarType = SecurityService::CALENDAR_TYPE_NO_DATES;
                if (in_array($id, $secServFromMaintenanceDates)) {
                    $maintenanceCalendarType = SecurityService::CALENDAR_TYPE_SPECIFIC_DATES;
                    $exec = true;
                }

                if ($exec) {
                    $ret &= (bool)$this->execute("UPDATE `security_services` SET `audit_calendar_type`={$auditCalendarType}, `maintenance_calendar_type`={$maintenanceCalendarType} WHERE `id`={$id}");
                }
            }

            if (!$ret) {
                throw new Exception('Migration calendar type of security services failed', 1);
                return false;
            }
        }
    }

    public function down()
    {
        $this->table('security_services')
            ->removeColumn('audit_calendar_type')
            ->removeColumn('audit_calendar_recurrence_start_date')
            ->removeColumn('audit_calendar_recurrence_frequency')
            ->removeColumn('audit_calendar_recurrence_period')
            ->removeColumn('maintenance_calendar_type')
            ->removeColumn('maintenance_calendar_recurrence_start_date')
            ->removeColumn('maintenance_calendar_recurrence_frequency')
            ->removeColumn('maintenance_calendar_recurrence_period')
            ->update();
    }
}
