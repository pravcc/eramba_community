<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartyRiskOvertimeGraph seed.
 */
class ThirdPartyRiskOvertimeGraphSeed extends AbstractSeed
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
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '2',
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1492041611',
                'created' => '2017-04-13 00:00:11',
            ],
            [
                'id' => '3',
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1492128012',
                'created' => '2017-04-14 00:00:12',
            ],
            [
                'id' => '4',
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1492214414',
                'created' => '2017-04-15 00:00:14',
            ],
            [
                'id' => '5',
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1492300810',
                'created' => '2017-04-16 00:00:10',
            ],
            [
                'id' => '6',
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1492387215',
                'created' => '2017-04-17 00:00:15',
            ],
            [
                'id' => '7',
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1492473612',
                'created' => '2017-04-18 00:00:12',
            ],
            [
                'id' => '8',
                'risk_count' => '1',
                'risk_score' => '6',
                'residual_score' => '4',
                'timestamp' => '1492560014',
                'created' => '2017-04-19 00:00:14',
            ],
        ];

        $table = $this->table('third_party_risk_overtime_graphs');
        $table->insert($data)->save();
    }
}
