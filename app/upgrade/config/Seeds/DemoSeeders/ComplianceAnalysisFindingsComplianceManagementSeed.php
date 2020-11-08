<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceAnalysisFindingsComplianceManagement seed.
 */
class ComplianceAnalysisFindingsComplianceManagementSeed extends AbstractSeed
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
                'id' => '2',
                'compliance_analysis_finding_id' => '1',
                'compliance_management_id' => '15',
            ],
        ];

        $table = $this->table('compliance_analysis_findings_compliance_managements');
        $table->insert($data)->save();
    }
}
