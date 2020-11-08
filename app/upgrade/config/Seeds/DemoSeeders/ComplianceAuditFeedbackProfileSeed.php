<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceAuditFeedbackProfile seed.
 */
class ComplianceAuditFeedbackProfileSeed extends AbstractSeed
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
                'name' => 'Yes-No-NA',
                'compliance_audit_feedback_count' => '3',
                'created' => '2017-04-10 16:01:06',
                'modified' => '2017-04-10 16:01:06',
            ],
        ];

        $table = $this->table('compliance_audit_feedback_profiles');
        $table->insert($data)->save();
    }
}
