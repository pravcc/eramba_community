<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceStatus seed.
 */
class ComplianceStatusSeed extends AbstractSeed
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
                'name' => 'On-Going',
            ],
            [
                'id' => '2',
                'name' => 'Compliant',
            ],
            [
                'id' => '3',
                'name' => 'Non-Compliant',
            ],
            [
                'id' => '4',
                'name' => 'Not-Applicable',
            ],
        ];

        $table = $this->table('compliance_statuses');
        $table->insert($data)->save();
    }
}
