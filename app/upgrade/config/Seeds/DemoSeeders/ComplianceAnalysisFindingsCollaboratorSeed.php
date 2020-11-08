<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceAnalysisFindingsCollaborator seed.
 */
class ComplianceAnalysisFindingsCollaboratorSeed extends AbstractSeed
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
                'collaborator_id' => '2',
            ],
        ];

        $table = $this->table('compliance_analysis_findings_collaborators');
        $table->insert($data)->save();
    }
}
