<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessContinuityPlanAuditDate seed.
 */
class BusinessContinuityPlanAuditDateSeed extends AbstractSeed
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
                'day' => '12',
                'month' => '9',
            ],
        ];

        $table = $this->table('business_continuity_plan_audit_dates');
        $table->insert($data)->save();
    }
}
