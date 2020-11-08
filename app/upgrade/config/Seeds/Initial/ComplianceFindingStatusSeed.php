<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceFindingStatus seed.
 */
class ComplianceFindingStatusSeed extends AbstractSeed
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
                'name' => 'Open Item',
            ],
            [
                'id' => '2',
                'name' => 'Closed Item',
            ],
        ];

        $table = $this->table('compliance_finding_statuses');
        $table->insert($data)->save();
    }
}
