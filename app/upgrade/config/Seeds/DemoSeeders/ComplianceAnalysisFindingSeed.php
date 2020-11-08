<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceAnalysisFinding seed.
 */
class ComplianceAnalysisFindingSeed extends AbstractSeed
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
                'title' => 'Change Request Procedure Missing',
                'description' => 'We are missing a policy that describes in detail how our change request procedure works.',
                'due_date' => '2017-07-19',
                'status' => '1',
                'created' => '2017-04-10 14:28:04',
                'modified' => '2017-04-10 14:40:24',
            ],
        ];

        $table = $this->table('compliance_analysis_findings');
        $table->insert($data)->save();
    }
}
