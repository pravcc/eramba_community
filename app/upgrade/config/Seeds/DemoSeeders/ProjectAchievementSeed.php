<?php
use Phinx\Seed\AbstractSeed;

/**
 * ProjectAchievement seed.
 */
class ProjectAchievementSeed extends AbstractSeed
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
                'description' => 'Review our architecture and define how much and what type traffic we need to analyse.',
                'date' => '2017-05-31',
                'expired' => '0',
                'completion' => '40',
                'project_id' => '1',
                'task_order' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:15:06',
                'modified' => '2017-04-11 13:15:06',
            ],
            [
                'id' => '2',
                'user_id' => '4',
                'description' => 'Identify proper vendors',
                'date' => '2017-08-31',
                'expired' => '0',
                'completion' => '40',
                'project_id' => '1',
                'task_order' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:15:30',
                'modified' => '2017-04-11 13:15:30',
            ],
            [
                'id' => '3',
                'user_id' => '4',
                'description' => 'Purchase an IDS solution',
                'date' => '2017-10-31',
                'expired' => '0',
                'completion' => '0',
                'project_id' => '1',
                'task_order' => '3',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:17:43',
                'modified' => '2017-04-11 13:17:43',
            ],
        ];

        $table = $this->table('project_achievements');
        $table->insert($data)->save();
    }
}
