<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskCalculation seed.
 */
class RiskCalculationSeed extends AbstractSeed
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
                'model' => 'Risk',
                'method' => 'eramba',
                'modified' => '2017-04-13 18:35:41',
            ],
            [
                'id' => '2',
                'model' => 'ThirdPartyRisk',
                'method' => 'eramba',
                'modified' => '2017-04-11 13:23:46',
            ],
            [
                'id' => '3',
                'model' => 'BusinessContinuity',
                'method' => 'eramba',
                'modified' => '2016-12-12 17:08:04',
            ],
        ];

        $table = $this->table('risk_calculations');
        $table->insert($data)->save();
    }
}
