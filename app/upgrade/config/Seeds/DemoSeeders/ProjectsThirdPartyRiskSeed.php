<?php
use Phinx\Seed\AbstractSeed;

/**
 * ProjectsThirdPartyRisk seed.
 */
class ProjectsThirdPartyRiskSeed extends AbstractSeed
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
                'project_id' => '1',
                'third_party_risk_id' => '1',
                'created' => '2017-04-11 13:25:23',
            ],
        ];

        $table = $this->table('projects_third_party_risks');
        $table->insert($data)->save();
    }
}
