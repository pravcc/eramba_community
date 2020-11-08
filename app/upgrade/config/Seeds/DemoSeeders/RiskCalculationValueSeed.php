<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskCalculationValue seed.
 */
class RiskCalculationValueSeed extends AbstractSeed
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
                'risk_calculation_id' => '2',
                'field' => 'default',
                'value' => '1',
                'created' => '2017-04-11 13:23:46',
                'modified' => '2017-04-11 13:23:46',
            ],
            [
                'id' => '4',
                'risk_calculation_id' => '2',
                'field' => 'default',
                'value' => '2',
                'created' => '2017-04-11 13:23:46',
                'modified' => '2017-04-11 13:23:46',
            ],
            [
                'id' => '7',
                'risk_calculation_id' => '1',
                'field' => 'default',
                'value' => '1',
                'created' => '2017-04-13 18:35:41',
                'modified' => '2017-04-13 18:35:41',
            ],
            [
                'id' => '8',
                'risk_calculation_id' => '1',
                'field' => 'default',
                'value' => '2',
                'created' => '2017-04-13 18:35:41',
                'modified' => '2017-04-13 18:35:41',
            ],
        ];

        $table = $this->table('risk_calculation_values');
        $table->insert($data)->save();
    }
}
