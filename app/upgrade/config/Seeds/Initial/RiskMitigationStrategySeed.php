<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskMitigationStrategy seed.
 */
class RiskMitigationStrategySeed extends AbstractSeed
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
                'name' => 'Accept',
            ],
            [
                'id' => '2',
                'name' => 'Avoid',
            ],
            [
                'id' => '3',
                'name' => 'Mitigate',
            ],
            [
                'id' => '4',
                'name' => 'Transfer',
            ],
        ];

        $table = $this->table('risk_mitigation_strategies');
        $table->insert($data)->save();
    }
}
