<?php
use Phinx\Seed\AbstractSeed;

/**
 * Project seed.
 */
class ProjectSeed extends AbstractSeed
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
                'title' => 'Snort (IDS) Implementation',
                'goal' => 'We want to implement IDS on our internet gateways.',
                'start' => '2017-04-11',
                'deadline' => '2017-09-30',
                'plan_budget' => '45000',
                'project_status_id' => '2',
                'user_id' => '2',
                'over_budget' => '0',
                'expired_tasks' => '0',
                'expired' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:14:34',
                'modified' => '2017-04-11 13:14:34',
            ],
        ];

        $table = $this->table('projects');
        $table->insert($data)->save();
    }
}
