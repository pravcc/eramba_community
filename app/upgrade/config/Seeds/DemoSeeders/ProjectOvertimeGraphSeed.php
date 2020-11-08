<?php
use Phinx\Seed\AbstractSeed;

/**
 * ProjectOvertimeGraph seed.
 */
class ProjectOvertimeGraphSeed extends AbstractSeed
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
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '2',
                'project_id' => '1',
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1492041611',
                'created' => '2017-04-13 00:00:11',
            ],
            [
                'id' => '3',
                'project_id' => '1',
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1492128012',
                'created' => '2017-04-14 00:00:12',
            ],
            [
                'id' => '4',
                'project_id' => '1',
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1492214414',
                'created' => '2017-04-15 00:00:14',
            ],
            [
                'id' => '5',
                'project_id' => '1',
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1492300810',
                'created' => '2017-04-16 00:00:10',
            ],
            [
                'id' => '6',
                'project_id' => '1',
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1492387214',
                'created' => '2017-04-17 00:00:14',
            ],
            [
                'id' => '7',
                'project_id' => '1',
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1492473612',
                'created' => '2017-04-18 00:00:12',
            ],
            [
                'id' => '8',
                'project_id' => '1',
                'current_budget' => '0',
                'budget' => '45000',
                'timestamp' => '1492560013',
                'created' => '2017-04-19 00:00:13',
            ],
        ];

        $table = $this->table('project_overtime_graphs');
        $table->insert($data)->save();
    }
}
