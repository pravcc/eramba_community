<?php
use Phinx\Seed\AbstractSeed;

/**
 * RisksThreat seed.
 */
class RisksThreatSeed extends AbstractSeed
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
                'id' => '5',
                'risk_id' => '2',
                'threat_id' => '33',
            ],
            [
                'id' => '6',
                'risk_id' => '2',
                'threat_id' => '9',
            ],
            [
                'id' => '7',
                'risk_id' => '2',
                'threat_id' => '23',
            ],
            [
                'id' => '8',
                'risk_id' => '2',
                'threat_id' => '22',
            ],
            [
                'id' => '9',
                'risk_id' => '2',
                'threat_id' => '21',
            ],
            [
                'id' => '10',
                'risk_id' => '2',
                'threat_id' => '5',
            ],
            [
                'id' => '11',
                'risk_id' => '2',
                'threat_id' => '14',
            ],
            [
                'id' => '12',
                'risk_id' => '2',
                'threat_id' => '8',
            ],
            [
                'id' => '13',
                'risk_id' => '2',
                'threat_id' => '4',
            ],
            [
                'id' => '14',
                'risk_id' => '2',
                'threat_id' => '15',
            ],
            [
                'id' => '15',
                'risk_id' => '2',
                'threat_id' => '10',
            ],
            [
                'id' => '16',
                'risk_id' => '1',
                'threat_id' => '5',
            ],
            [
                'id' => '17',
                'risk_id' => '1',
                'threat_id' => '14',
            ],
            [
                'id' => '18',
                'risk_id' => '1',
                'threat_id' => '4',
            ],
            [
                'id' => '19',
                'risk_id' => '1',
                'threat_id' => '15',
            ],
        ];

        $table = $this->table('risks_threats');
        $table->insert($data)->save();
    }
}
