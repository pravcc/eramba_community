<?php
use Migrations\AbstractMigration;

class ChangeValueOfCalendarTypesBasedOnOldDates extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('Hash', 'Utility');
            App::uses('SecurityService', 'Model');

            $SecurityService = ClassRegistry::init('SecurityService');

            $securityServices = $SecurityService->find('all', [
                'fields' => [
                    'id', 'audit_calendar_type', 'maintenance_calendar_type'
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
            
            $secServFromAuditDates = Hash::extract($auditDates, '{n}.SecurityServiceAuditDate.security_service_id');
            $secServFromMaintenanceDates = Hash::extract($maintenanceDates, '{n}.SecurityServiceMaintenanceDate.security_service_id');
            
            $ret = true;
            foreach ($securityServices as $ss) {
                $id = $ss['SecurityService']['id'];
                $oldAuditCalendarType = $ss['SecurityService']['audit_calendar_type'];
                $oldMaintenanceCalendarType = $ss['SecurityService']['maintenance_calendar_type'];
                $values = [];

                if (in_array($id, $secServFromAuditDates) && $oldAuditCalendarType == SecurityService::CALENDAR_TYPE_NO_DATES) {
                    $values['audit_calendar_type'] = SecurityService::CALENDAR_TYPE_SPECIFIC_DATES;
                }

                if (in_array($id, $secServFromMaintenanceDates) && $oldMaintenanceCalendarType == SecurityService::CALENDAR_TYPE_NO_DATES) {
                    $values['maintenance_calendar_type'] = SecurityService::CALENDAR_TYPE_SPECIFIC_DATES;
                }

                if (!empty($values)) {
                    $query = "UPDATE `security_services` SET";
                    $isFirst = true;
                    foreach ($values as $key => $val) {
                        if (!$isFirst) {
                            $query .= ",";
                        } else {
                            $isFirst = false;
                        }

                        $query .= " `{$key}`={$val}";
                    }
                    $query .= " WHERE `id`={$id}";

                    $ret &= (bool)$this->execute($query);
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
    }
}
