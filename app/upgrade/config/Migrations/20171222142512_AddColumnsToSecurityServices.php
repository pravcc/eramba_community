<?php
use Phinx\Migration\AbstractMigration;

class AddColumnsToSecurityServices extends AbstractMigration
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
        $table = $this->table('security_services');
        $table->addColumn('audit_owner_id', 'integer', [
            'after' => 'audit_success_criteria',
            'default' => 1,
            'limit' => 11,
            'null' => false,
        ])->addForeignKey(
            'audit_owner_id',
            'users',
            'id',
            [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ]
        );
        $table->addColumn('audit_evidence_owner_id', 'integer', [
            'after' => 'audit_owner_id',
            'default' => 1,
            'limit' => 11,
            'null' => false,
        ])->addForeignKey(
            'audit_evidence_owner_id',
            'users',
            'id',
            [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ]
        );
        $table->addColumn('maintenance_owner_id', 'integer', [
            'after' => 'maintenance_metric_description',
            'default' => 1,
            'limit' => 11,
            'null' => false,
        ])->addForeignKey(
            'maintenance_owner_id',
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
