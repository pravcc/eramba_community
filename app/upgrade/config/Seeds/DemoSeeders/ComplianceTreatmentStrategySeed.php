<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceTreatmentStrategy seed.
 */
class ComplianceTreatmentStrategySeed extends AbstractSeed
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
                'name' => 'Compliant',
            ],
            [
                'id' => '2',
                'name' => 'Not Applicable',
            ],
            [
                'id' => '3',
                'name' => 'Not Compliant',
            ],
        ];

        $table = $this->table('compliance_treatment_strategies');
        $table->insert($data)->save();
    }
}
