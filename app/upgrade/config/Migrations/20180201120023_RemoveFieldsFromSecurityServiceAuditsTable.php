<?php
use Phinx\Migration\AbstractMigration;

class RemoveFieldsFromSecurityServiceAuditsTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'AuditOwner' => 'user_id',
                'AuditEvidenceOwner' => 'audit_evidence_owner_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'SecurityServiceAudit', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('security_service_audits')
            ->dropForeignKey([], 'security_service_audits_ibfk_4')
            ->dropForeignKey([], 'security_service_audits_ibfk_5')
            ->removeIndexByName('audit_evidence_owner_id')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('security_service_audits')
            ->removeColumn('user_id')
            ->removeColumn('audit_evidence_owner_id')
            ->update();
    }

    public function down()
    {
        $this->table('security_service_audits')
            ->addColumn('user_id', 'integer', [
                'after' => 'result_description',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('audit_evidence_owner_id', 'integer', [
                'after' => 'user_id',
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
                    'user_id',
                ],
                [
                    'name' => 'user_id',
                ]
            )
            ->update();

        $this->table('security_service_audits')
            ->addForeignKey(
                'audit_evidence_owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'security_service_audits_ibfk_4'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'security_service_audits_ibfk_5'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

