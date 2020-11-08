<?php
use Phinx\Seed\AbstractSeed;

/**
 * Legal seed.
 */
class LegalSeed extends AbstractSeed
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
                'name' => 'Sarbanes Oxley',
                'description' => '',
                'risk_magnifier' => '5.6',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 14:56:15',
                'modified' => '2017-04-10 14:56:15',
            ],
            [
                'id' => '2',
                'name' => 'EU GDPR',
                'description' => '',
                'risk_magnifier' => '3.4',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-10 14:56:28',
                'modified' => '2017-04-10 14:56:28',
            ],
        ];

        $table = $this->table('legals');
        $table->insert($data)->save();
    }
}
