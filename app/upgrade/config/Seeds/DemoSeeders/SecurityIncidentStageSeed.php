<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityIncidentStage seed.
 */
class SecurityIncidentStageSeed extends AbstractSeed
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
                'id' => '2',
                'name' => 'Identification',
                'description' => 'In the identification phase you need to work out whether you are dealing with an event or an incident. This is where understanding your environment is critical as it means looking for significant deviations from "normal" traffic baselines or other methods.',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:08:58',
                'modified' => '2017-04-11 13:08:58',
            ],
            [
                'id' => '3',
                'name' => 'Containment',
                'description' => 'Deuble says that as you head into the containment stage you will want to work with the business to limit the damage caused to systems and prevent any further damage from occurring. This includes short and long term containment activities.',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:09:11',
                'modified' => '2017-04-11 13:09:11',
            ],
            [
                'id' => '4',
                'name' => 'Recovery',
                'description' => 'At this point, it’s time to determine when to bring the system back in to production and how long we monitor the system for any signs of abnormal activity.',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:09:22',
                'modified' => '2017-04-11 13:09:22',
            ],
            [
                'id' => '5',
                'name' => 'Lessons Learned',
                'description' => 'This final stage is often skipped as the business moves back into normal operations but it’s critical to look back and heed the lessons learned. These lessons will allow you to incorporate additional activities and knowledge back into your incident response process to produce better future outcomes and additional defenses.',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:09:36',
                'modified' => '2017-04-11 13:09:36',
            ],
        ];

        $table = $this->table('security_incident_stages');
        $table->insert($data)->save();
    }
}
