<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdParty seed.
 */
class ThirdPartySeed extends AbstractSeed
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
                'name' => 'None',
                'description' => '',
                'third_party_type_id' => NULL,
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '0',
                'workflow_owner_id' => NULL,
                '_hidden' => '1',
                'created' => '2015-12-19 00:00:00',
                'modified' => '2015-12-19 00:00:00',
            ],
        ];

        $table = $this->table('third_parties');
        $table->insert($data)->save();
    }
}
