<?php
use Phinx\Seed\AbstractSeed;

/**
 * BulkAction seed.
 */
class BulkActionSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'type' => '1',
                'model' => 'ComplianceAuditSetting',
                'json_data' => '{"BulkAction":{"model":"ComplianceAuditSetting","type":"1","no_change":{"status":"1","Auditee":"0","compliance_audit_feedback_profile_id":"1"},"apply_all":"1","apply_id":["268","269","270","271","272","273","274","275"],"modified":"2017-04-12 21:32:42","created":"2017-04-12 21:32:42"},"":{"User":{"id":"1"}},"BulkActionObject":[{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"268"}},{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"269"}},{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"270"}},{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"271"}},{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"272"}},{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"273"}},{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"274"}},{"BulkActionObject":{"model":"ComplianceAuditSetting","foreign_key":"275"}}]}',
                'user_id' => '1',
                'created' => '2017-04-12 21:32:42',
                'modified' => '2017-04-12 21:32:42',
            ],
        ];

        $table = $this->table('bulk_actions');
        $table->insert($data)->save();
    }
}
