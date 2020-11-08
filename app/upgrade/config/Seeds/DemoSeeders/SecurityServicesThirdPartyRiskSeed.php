<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityServicesThirdPartyRisk seed.
 */
class SecurityServicesThirdPartyRiskSeed extends AbstractSeed
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
                'security_service_id' => '11',
                'third_party_risk_id' => '1',
            ],
            [
                'id' => '2',
                'security_service_id' => '24',
                'third_party_risk_id' => '1',
            ],
        ];

        $table = $this->table('security_services_third_party_risks');
        $table->insert($data)->save();
    }
}
