<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartyIncidentOvertimeGraph seed.
 */
class ThirdPartyIncidentOvertimeGraphSeed extends AbstractSeed
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
                'third_party_id' => '2',
                'security_incident_count' => '0',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '2',
                'third_party_id' => '3',
                'security_incident_count' => '0',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '3',
                'third_party_id' => '4',
                'security_incident_count' => '0',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '4',
                'third_party_id' => '5',
                'security_incident_count' => '0',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '5',
                'third_party_id' => '6',
                'security_incident_count' => '0',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
        ];

        $table = $this->table('third_party_incident_overtime_graphs');
        $table->insert($data)->save();
    }
}
