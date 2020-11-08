<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartyRisksThreat seed.
 */
class ThirdPartyRisksThreatSeed extends AbstractSeed
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
                'third_party_risk_id' => '1',
                'threat_id' => '33',
            ],
            [
                'id' => '2',
                'third_party_risk_id' => '1',
                'threat_id' => '9',
            ],
            [
                'id' => '3',
                'third_party_risk_id' => '1',
                'threat_id' => '23',
            ],
            [
                'id' => '4',
                'third_party_risk_id' => '1',
                'threat_id' => '22',
            ],
            [
                'id' => '5',
                'third_party_risk_id' => '1',
                'threat_id' => '21',
            ],
            [
                'id' => '6',
                'third_party_risk_id' => '1',
                'threat_id' => '5',
            ],
            [
                'id' => '7',
                'third_party_risk_id' => '1',
                'threat_id' => '14',
            ],
            [
                'id' => '8',
                'third_party_risk_id' => '1',
                'threat_id' => '8',
            ],
            [
                'id' => '9',
                'third_party_risk_id' => '1',
                'threat_id' => '4',
            ],
            [
                'id' => '10',
                'third_party_risk_id' => '1',
                'threat_id' => '15',
            ],
            [
                'id' => '11',
                'third_party_risk_id' => '1',
                'threat_id' => '10',
            ],
        ];

        $table = $this->table('third_party_risks_threats');
        $table->insert($data)->save();
    }
}
