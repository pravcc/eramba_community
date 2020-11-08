<?php
use Phinx\Seed\AbstractSeed;

/**
 * Process seed.
 */
class ProcessSeed extends AbstractSeed
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
                'business_unit_id' => '2',
                'name' => 'Reporting',
                'description' => '',
                'rto' => '7',
                'rpo' => '1',
                'rpd' => '10',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:35:25',
                'modified' => '2017-04-10 13:35:25',
            ],
            [
                'id' => '2',
                'business_unit_id' => '2',
                'name' => 'Payments',
                'description' => '',
                'rto' => '2',
                'rpo' => '1',
                'rpd' => '45',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:35:42',
                'modified' => '2017-04-10 13:35:42',
            ],
            [
                'id' => '3',
                'business_unit_id' => '2',
                'name' => 'Investment Management',
                'description' => '',
                'rto' => '7',
                'rpo' => '10',
                'rpd' => '45',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:36:01',
                'modified' => '2017-04-10 13:36:01',
            ],
            [
                'id' => '4',
                'business_unit_id' => '2',
                'name' => 'Tax Services',
                'description' => '',
                'rto' => '10',
                'rpo' => '12',
                'rpd' => '156',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:36:20',
                'modified' => '2017-04-10 13:36:20',
            ],
            [
                'id' => '5',
                'business_unit_id' => '3',
                'name' => 'People Management',
                'description' => '',
                'rto' => '10',
                'rpo' => '12',
                'rpd' => '10',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:36:57',
                'modified' => '2017-04-10 13:36:57',
            ],
            [
                'id' => '6',
                'business_unit_id' => '3',
                'name' => 'Carrer Development',
                'description' => '',
                'rto' => '10',
                'rpo' => '12',
                'rpd' => '-3',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:37:09',
                'modified' => '2017-04-10 13:37:09',
            ],
            [
                'id' => '7',
                'business_unit_id' => '4',
                'name' => 'Deploy Applications',
                'description' => '',
                'rto' => '12',
                'rpo' => '10',
                'rpd' => '0',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:37:53',
                'modified' => '2017-04-10 13:37:53',
            ],
            [
                'id' => '8',
                'business_unit_id' => '4',
                'name' => 'Support Application Users',
                'description' => '',
                'rto' => '10',
                'rpo' => '45',
                'rpd' => '134',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 13:38:11',
                'modified' => '2017-04-10 13:38:11',
            ],
        ];

        $table = $this->table('processes');
        $table->insert($data)->save();
    }
}
