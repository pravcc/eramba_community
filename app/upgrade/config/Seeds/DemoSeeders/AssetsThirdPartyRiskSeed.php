<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetsThirdPartyRisk seed.
 */
class AssetsThirdPartyRiskSeed extends AbstractSeed
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
                'asset_id' => '1',
                'third_party_risk_id' => '1',
            ],
            [
                'id' => '2',
                'asset_id' => '3',
                'third_party_risk_id' => '1',
            ],
            [
                'id' => '3',
                'asset_id' => '10',
                'third_party_risk_id' => '1',
            ],
        ];

        $table = $this->table('assets_third_party_risks');
        $table->insert($data)->save();
    }
}
