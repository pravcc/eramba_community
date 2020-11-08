<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityIncidentStagesSecurityIncident seed.
 */
class SecurityIncidentStagesSecurityIncidentSeed extends AbstractSeed
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
                'security_incident_stage_id' => '2',
                'security_incident_id' => '1',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:10:29',
                'modified' => '2017-04-11 13:10:40',
            ],
            [
                'id' => '2',
                'security_incident_stage_id' => '3',
                'security_incident_id' => '1',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:10:29',
                'modified' => '2017-04-11 13:10:43',
            ],
            [
                'id' => '3',
                'security_incident_stage_id' => '4',
                'security_incident_id' => '1',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:10:29',
                'modified' => '2017-04-11 13:10:46',
            ],
            [
                'id' => '4',
                'security_incident_stage_id' => '5',
                'security_incident_id' => '1',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:10:29',
                'modified' => '2017-04-11 13:10:49',
            ],
            [
                'id' => '5',
                'security_incident_stage_id' => '2',
                'security_incident_id' => '2',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:12:25',
                'modified' => '2017-04-12 09:06:00',
            ],
            [
                'id' => '6',
                'security_incident_stage_id' => '3',
                'security_incident_id' => '2',
                'status' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:12:25',
                'modified' => '2017-04-11 13:12:25',
            ],
            [
                'id' => '7',
                'security_incident_stage_id' => '4',
                'security_incident_id' => '2',
                'status' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:12:25',
                'modified' => '2017-04-11 13:12:25',
            ],
            [
                'id' => '8',
                'security_incident_stage_id' => '5',
                'security_incident_id' => '2',
                'status' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:12:25',
                'modified' => '2017-04-11 13:12:25',
            ],
        ];

        $table = $this->table('security_incident_stages_security_incidents');
        $table->insert($data)->save();
    }
}
