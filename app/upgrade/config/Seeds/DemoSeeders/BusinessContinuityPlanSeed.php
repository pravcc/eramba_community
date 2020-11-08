<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessContinuityPlan seed.
 */
class BusinessContinuityPlanSeed extends AbstractSeed
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
                'title' => 'Service Delivery in the event of Pandemic / Transport Disruption',
                'objective' => 'Provide services to our customers in the scenario where they are not able to come to the office.',
                'audit_metric' => 'Services to our customers should be not interrupted',
                'audit_success_criteria' => 'Trigger the BCM plan',
                'launch_criteria' => 'A clear pandemic or transport disruption affects the city where our offices are located.',
                'security_service_type_id' => '4',
                'opex' => '13000',
                'capex' => '0',
                'resource_utilization' => '13',
                'regular_review' => '0000-00-00',
                'awareness_recurrence' => NULL,
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'ongoing_corrective_actions' => '0',
                'launch_responsible_id' => '5',
                'sponsor_id' => '4',
                'owner_id' => '3',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-12 12:05:22',
                'modified' => '2017-04-12 12:05:22',
            ],
        ];

        $table = $this->table('business_continuity_plans');
        $table->insert($data)->save();
    }
}
