<?php
use Phinx\Migration\AbstractMigration;

class RemoveFieldsFromSecurityServicesTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'ServiceOwner' => 'user_id',
                'AuditOwner' => 'audit_owner_id',
                'AuditEvidenceOwner' => 'audit_evidence_owner_id',
                'MaintenanceOwner' => 'maintenance_owner_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'SecurityService', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('security_services')
            ->dropForeignKey([], 'security_services_ibfk_3')
            ->dropForeignKey([], 'security_services_ibfk_5')
            ->dropForeignKey([], 'security_services_ibfk_6')
            ->dropForeignKey([], 'security_services_ibfk_7')
            ->removeIndexByName('audit_evidence_owner_id')
            ->removeIndexByName('audit_owner_id')
            ->removeIndexByName('maintenance_owner_id')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('security_services')
            ->removeColumn('user_id')
            ->removeColumn('audit_owner_id')
            ->removeColumn('audit_evidence_owner_id')
            ->removeColumn('maintenance_owner_id')
            ->update();
    }

    public function down()
    {

        $this->table('security_services')
            ->addColumn('user_id', 'integer', [
                'after' => 'service_classification_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('audit_owner_id', 'integer', [
                'after' => 'audit_success_criteria',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('audit_evidence_owner_id', 'integer', [
                'after' => 'audit_owner_id',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('maintenance_owner_id', 'integer', [
                'after' => 'maintenance_metric_description',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'audit_evidence_owner_id',
                ],
                [
                    'name' => 'audit_evidence_owner_id',
                ]
            )
            ->addIndex(
                [
                    'audit_owner_id',
                ],
                [
                    'name' => 'audit_owner_id',
                ]
            )
            ->addIndex(
                [
                    'maintenance_owner_id',
                ],
                [
                    'name' => 'maintenance_owner_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'user_id',
                ]
            )
            ->update();

        $this->table('security_services')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL',
                    'constraint' => 'security_services_ibfk_3'
                ]
            )
            ->addForeignKey(
                'audit_owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'security_services_ibfk_5'
                ]
            )
            ->addForeignKey(
                'audit_evidence_owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'security_services_ibfk_6'
                ]
            )
            ->addForeignKey(
                'maintenance_owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'security_services_ibfk_7'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

