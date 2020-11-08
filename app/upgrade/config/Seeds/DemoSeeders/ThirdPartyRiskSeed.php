<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartyRisk seed.
 */
class ThirdPartyRiskSeed extends AbstractSeed
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
                'title' => 'Virus, Malware, Spyware from Contractors Laptops',
                'shared_information' => 'NA',
                'controlled' => 'NA',
                'threats' => '',
                'vulnerabilities' => '',
                'residual_score' => '70',
                'risk_score' => '6',
                'risk_score_formula' => '3 + 3 = 6',
                'residual_risk' => '4.2',
                'user_id' => '3',
                'guardian_id' => '3',
                'review' => '2017-08-31',
                'expired' => '0',
                'exceptions_issues' => '0',
                'controls_issues' => '0',
                'control_in_design' => '0',
                'expired_reviews' => '0',
                'risk_above_appetite' => '0',
                'risk_mitigation_strategy_id' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:25:23',
                'modified' => '2017-04-13 18:33:40',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
        ];

        $table = $this->table('third_party_risks');
        $table->insert($data)->save();
    }
}
