<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskClassificationsRisk seed.
 */
class RiskClassificationsRiskSeed extends AbstractSeed
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
                'id' => '3',
                'risk_classification_id' => '5',
                'risk_id' => '2',
            ],
            [
                'id' => '4',
                'risk_classification_id' => '4',
                'risk_id' => '2',
            ],
            [
                'id' => '5',
                'risk_classification_id' => '5',
                'risk_id' => '1',
            ],
            [
                'id' => '6',
                'risk_classification_id' => '3',
                'risk_id' => '1',
            ],
        ];

        $table = $this->table('risk_classifications_risks');
        $table->insert($data)->save();
    }
}
