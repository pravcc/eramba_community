<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartiesThirdPartyRisk seed.
 */
class ThirdPartiesThirdPartyRiskSeed extends AbstractSeed
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
                'third_party_id' => '6',
            ],
        ];

        $table = $this->table('third_parties_third_party_risks');
        $table->insert($data)->save();
    }
}
