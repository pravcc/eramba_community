<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessContinuityTask seed.
 */
class BusinessContinuityTaskSeed extends AbstractSeed
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
                'step' => '1',
                'when' => 'As soon as the decision to trigger the plan has been made',
                'who' => 'The plan owner',
                'awareness_role' => '3',
                'does' => 'Communicates by phone to all PMs that they are not allowed to come to the office and that message should be pass to their respective teams',
                'where' => 'From wherever that person is',
                'how' => 'By phone',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-12 12:06:35',
                'modified' => '2017-04-12 12:06:35',
            ],
            [
                'id' => '2',
                'business_continuity_plan_id' => '1',
                'step' => '2',
                'when' => 'After PMs have been communicated',
                'who' => 'Office Manager',
                'awareness_role' => '5',
                'does' => 'Communicates to building facility operators that the office is to remain closed.',
                'where' => 'From wherever that person is.',
                'how' => 'Over the phone',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-12 12:07:26',
                'modified' => '2017-04-12 12:07:26',
            ],
            [
                'id' => '3',
                'business_continuity_plan_id' => '1',
                'step' => '3',
                'when' => 'After PM has been communicated',
                'who' => 'PMs',
                'awareness_role' => '5',
                'does' => 'Communicates to staff that they must work from home',
                'where' => 'Wherever that person is',
                'how' => 'Over email and phone',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-12 12:08:17',
                'modified' => '2017-04-12 12:08:17',
            ],
        ];

        $table = $this->table('business_continuity_tasks');
        $table->insert($data)->save();
    }
}
