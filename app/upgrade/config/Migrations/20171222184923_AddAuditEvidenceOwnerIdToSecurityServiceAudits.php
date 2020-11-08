<?php
use Phinx\Migration\AbstractMigration;

class AddAuditEvidenceOwnerIdToSecurityServiceAudits extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('security_service_audits');
        $table->addColumn('audit_evidence_owner_id', 'integer', [
            'after' => 'user_id',
            'default' => 1,
            'limit' => 11,
            'null' => false
        ])->addForeignKey(
            'audit_evidence_owner_id',
            'users',
            'id',
            [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ]
        );
        $table->update();
    }
}
