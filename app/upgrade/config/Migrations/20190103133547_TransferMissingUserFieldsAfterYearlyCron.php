<?php
use Phinx\Migration\AbstractMigration;

class TransferMissingUserFieldsAfterYearlyCron extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');

            $ret = true;

            if (Configure::read('Eramba.version') === 'e2.0.2') {
                $ret &= $this->migrateSecurityServiceAuditsAndMaintenances();
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $errorMsg = "Error occured when migrating missing UserFields after yearly cron";
                CakeLog::write('error', $errorMsg);

                throw new Exception($errorMsg, 1);
                return false;
            }
        }
    }

    protected function migrateSecurityServiceAuditsAndMaintenances()
    {
        App::uses('SecurityService', 'Model');

        $SecurityService = ClassRegistry::init('SecurityService');

        $securityServices = $SecurityService->find('all', [
            'fields' => [
                'SecurityService.id'
            ],
            'contain' => [
                'AuditOwner' => [
                    'fields' => [
                        'id'
                    ]
                ],
                'AuditOwnerGroup' => [
                    'fields' => [
                        'id'
                    ]
                ],
                'AuditEvidenceOwner' => [
                    'fields' => [
                        'id'
                    ]
                ],
                'AuditEvidenceOwnerGroup' => [
                    'fields' => [
                        'id'
                    ]
                ],
                'MaintenanceOwner' => [
                    'fields' => [
                        'id'
                    ]
                ],
                'MaintenanceOwnerGroup' => [
                    'fields' => [
                        'id'
                    ]
                ],
                'SecurityServiceAudit' => [
                    'fields' => [
                        'SecurityServiceAudit.id', 'SecurityServiceAudit.security_service_id'
                    ],
                    'conditions' => [
                        'SecurityServiceAudit.planned_date >=' => '2019-01-01 00:00:00'
                    ]
                ],
                'SecurityServiceMaintenance' => [
                    'fields' => [
                        'SecurityServiceMaintenance.id', 'SecurityServiceMaintenance.security_service_id'
                    ],
                    'conditions' => [
                        'SecurityServiceMaintenance.planned_date >=' => '2019-01-01 00:00:00'
                    ]
                ]
            ],
            'recursive' => -1
        ]);

        $newSecServData = [];
        foreach ($securityServices as $ss) {
            //
            // Prepare audits
            $audits = [];
            $auditOwners = Hash::extract($ss['AuditOwner'], '{n}.id') ?: ['User-1'];
            $auditEvidenceOwners = Hash::extract($ss['AuditEvidenceOwner'], '{n}.id') ?: ['User-1'];
            foreach ($ss['SecurityServiceAudit'] as $ssAudit) {
                $audits[] = [
                    'SecurityServiceAudit' => [
                        'id' => $ssAudit['id'],
                        'AuditOwner' => $auditOwners,
                        'AuditEvidenceOwner' => $auditEvidenceOwners
                    ]
                ];
                
            }
            //
            
            //
            // Prepare maintenances
            $maintenances = [];
            $maintenanceOwners = Hash::extract($ss['MaintenanceOwner'], '{n}.id') ?: ['User-1'];
            foreach ($ss['SecurityServiceMaintenance'] as $ssMaintenance) {
                $maintenances[] = [
                    'SecurityServiceMaintenance' => [
                        'id' => $ssMaintenance['id'],
                        'MaintenanceOwner' => $maintenanceOwners
                    ]
                ];
            }
            //
            
            if (!empty($audits) || !empty($maintenances)) {
                $temp = [];
                $temp['id'] = $ss['SecurityService']['id'];
                if (!empty($audits)) {
                    $temp['SecurityServiceAudit'] = $audits;
                }
                if (!empty($maintenances)) {
                    $temp['SecurityServiceMaintenance'] = $maintenances;
                }

                $newSecServData[] = [
                    'SecurityService' => $temp
                ];
            }
        }

        $bool = $SecurityService->saveAll($newSecServData, [
            'deep' => true,
            'fieldList' => [
                'SecurityService' => ['id'],
                'SecurityServiceAudit' => [
                    'AuditOwner', 'AuditEvidenceOwner'
                ],
                'SecurityServiceMaintenance' => [
                    'MaintenanceOwner'
                ]
            ]
        ]);
        
        return $bool;
    }

    public function down()
    {
    }
}
