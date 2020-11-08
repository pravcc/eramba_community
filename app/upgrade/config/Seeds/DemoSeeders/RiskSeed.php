<?php
use Phinx\Seed\AbstractSeed;

/**
 * Risk seed.
 */
class RiskSeed extends AbstractSeed
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
                'title' => 'Stolen Employee Laptop',
                'threats' => '',
                'vulnerabilities' => '',
                'residual_score' => '70',
                'risk_score' => '0',
                'risk_score_formula' => '',
                'residual_risk' => '0',
                'user_id' => '2',
                'guardian_id' => '4',
                'review' => '2017-05-31',
                'expired' => '0',
                'exceptions_issues' => '0',
                'controls_issues' => '0',
                'control_in_design' => '0',
                'expired_reviews' => '0',
                'risk_above_appetite' => '0',
                'risk_mitigation_strategy_id' => '3',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:05:33',
                'modified' => '2017-04-13 18:35:41',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '2',
                'title' => 'Theft in the Office',
                'threats' => '',
                'vulnerabilities' => '',
                'residual_score' => '60',
                'risk_score' => '0',
                'risk_score_formula' => '',
                'residual_risk' => '0',
                'user_id' => '3',
                'guardian_id' => '3',
                'review' => '2017-08-31',
                'expired' => '0',
                'exceptions_issues' => '0',
                'controls_issues' => '0',
                'control_in_design' => '0',
                'expired_reviews' => '0',
                'risk_above_appetite' => '0',
                'risk_mitigation_strategy_id' => '3',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:07:37',
                'modified' => '2017-04-13 18:35:41',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
        ];

        $table = $this->table('risks');
        $table->insert($data)->save();
    }
}
