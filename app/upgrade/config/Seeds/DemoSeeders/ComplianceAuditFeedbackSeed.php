<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceAuditFeedback seed.
 */
class ComplianceAuditFeedbackSeed extends AbstractSeed
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
                'compliance_audit_feedback_profile_id' => '1',
                'name' => 'Yes - We are Compliant',
                'created' => '2017-04-10 16:01:06',
                'modified' => '2017-04-10 16:01:06',
            ],
            [
                'id' => '2',
                'compliance_audit_feedback_profile_id' => '1',
                'name' => 'Not Compliant',
                'created' => '2017-04-10 16:01:19',
                'modified' => '2017-04-10 16:01:19',
            ],
            [
                'id' => '3',
                'compliance_audit_feedback_profile_id' => '1',
                'name' => 'We are not sure?',
                'created' => '2017-04-10 16:01:27',
                'modified' => '2017-04-10 16:01:27',
            ],
        ];

        $table = $this->table('compliance_audit_feedbacks');
        $table->insert($data)->save();
    }
}
