<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskClassificationsThirdPartyRisk seed.
 */
class RiskClassificationsThirdPartyRiskSeed extends AbstractSeed
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
                'risk_classification_id' => '5',
                'third_party_risk_id' => '1',
            ],
            [
                'id' => '2',
                'risk_classification_id' => '3',
                'third_party_risk_id' => '1',
            ],
        ];

        $table = $this->table('risk_classifications_third_party_risks');
        $table->insert($data)->save();
    }
}
