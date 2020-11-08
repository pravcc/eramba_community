<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessUnit seed.
 */
class BusinessUnitSeed extends AbstractSeed
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
                'name' => 'Everyone',
                'description' => '',
                'workflow_status' => '0',
                'workflow_owner_id' => NULL,
                '_hidden' => '1',
                'created' => '2015-12-19 00:00:00',
                'modified' => '2015-12-19 00:00:00',
            ],
            [
                'id' => '2',
                'name' => 'Finance',
                'description' => 'Finance &amp; Accounting department in charge of looking after the finances and tax obligations of the organisation.',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-10 13:35:09',
                'modified' => '2017-04-10 13:38:53',
            ],
            [
                'id' => '3',
                'name' => 'Human Resources',
                'description' => 'Responsible for identifying and supporting the on-boarding of talents',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-10 13:36:38',
                'modified' => '2017-04-10 13:39:15',
            ],
            [
                'id' => '4',
                'name' => 'IT',
                'description' => 'Responsible to support the business with IT solutions',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-10 13:37:22',
                'modified' => '2017-04-10 13:39:31',
            ],
        ];

        $table = $this->table('business_units');
        $table->insert($data)->save();
    }
}
