<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskClassificationType seed.
 */
class RiskClassificationTypeSeed extends AbstractSeed
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
                'name' => 'Likelihood',
                'risk_classification_count' => '3',
            ],
            [
                'id' => '2',
                'name' => 'Impact',
                'risk_classification_count' => '3',
            ],
        ];

        $table = $this->table('risk_classification_types');
        $table->insert($data)->save();
    }
}
