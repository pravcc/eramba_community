<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessContinuityPlanAudit seed.
 */
class BusinessContinuityPlanAuditSeed extends AbstractSeed
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
                'business_continuity_plan_id' => '1',
                'audit_metric_description' => 'Services to our customers should be not interrupted',
                'audit_success_criteria' => 'Trigger the BCM plan',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-09-12',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-12 12:05:22',
                'modified' => '2017-04-12 12:05:22',
            ],
        ];

        $table = $this->table('business_continuity_plan_audits');
        $table->insert($data)->save();
    }
}
