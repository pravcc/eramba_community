<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceAuditAuditeeFeedback seed.
 */
class ComplianceAuditAuditeeFeedbackSeed extends AbstractSeed
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
                'user_id' => '3',
                'compliance_audit_setting_id' => '1',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '1',
                'created' => '2017-04-10 16:03:22',
                'modified' => '2017-04-10 16:03:22',
            ],
            [
                'id' => '2',
                'user_id' => '3',
                'compliance_audit_setting_id' => '3',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '2',
                'created' => '2017-04-10 16:03:26',
                'modified' => '2017-04-10 16:03:26',
            ],
            [
                'id' => '3',
                'user_id' => '3',
                'compliance_audit_setting_id' => '2',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '3',
                'created' => '2017-04-10 16:03:29',
                'modified' => '2017-04-10 16:03:29',
            ],
            [
                'id' => '4',
                'user_id' => '3',
                'compliance_audit_setting_id' => '4',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '2',
                'created' => '2017-04-10 16:03:33',
                'modified' => '2017-04-10 16:03:33',
            ],
            [
                'id' => '5',
                'user_id' => '3',
                'compliance_audit_setting_id' => '5',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '1',
                'created' => '2017-04-10 16:03:37',
                'modified' => '2017-04-10 16:03:37',
            ],
            [
                'id' => '6',
                'user_id' => '4',
                'compliance_audit_setting_id' => '10',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '2',
                'created' => '2017-04-11 14:01:50',
                'modified' => '2017-04-11 14:01:50',
            ],
            [
                'id' => '7',
                'user_id' => '3',
                'compliance_audit_setting_id' => '6',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '2',
                'created' => '2017-04-12 21:29:05',
                'modified' => '2017-04-12 21:29:05',
            ],
            [
                'id' => '8',
                'user_id' => '3',
                'compliance_audit_setting_id' => '7',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '1',
                'created' => '2017-04-12 21:29:09',
                'modified' => '2017-04-12 21:29:09',
            ],
            [
                'id' => '9',
                'user_id' => '3',
                'compliance_audit_setting_id' => '8',
                'compliance_audit_feedback_profile_id' => '1',
                'compliance_audit_feedback_id' => '2',
                'created' => '2017-04-12 21:29:12',
                'modified' => '2017-04-12 21:29:12',
            ],
        ];

        $table = $this->table('compliance_audit_auditee_feedbacks');
        $table->insert($data)->save();
    }
}
