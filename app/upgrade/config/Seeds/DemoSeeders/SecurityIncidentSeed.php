<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityIncident seed.
 */
class SecurityIncidentSeed extends AbstractSeed
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
                'title' => 'Stolen Laptop',
                'description' => 'A laptop was stolen while an employee was on business trips.',
                'user_id' => '2',
                'reporter' => 'john.foo',
                'victim' => 'john.foo',
                'open_date' => '2017-04-11',
                'closure_date' => '2017-04-11',
                'expired' => '0',
                'type' => 'incident',
                'security_incident_status_id' => '3',
                'auto_close_incident' => '1',
                'security_incident_classification_id' => NULL,
                'lifecycle_incomplete' => '0',
                'ongoing_incident' => '1',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-11 13:10:29',
                'modified' => '2017-04-11 13:10:49',
            ],
            [
                'id' => '2',
                'title' => 'Creeping Account identified',
                'description' => 'An account belonging to an employee that left the organisation was found to be active on our systems.',
                'user_id' => '3',
                'reporter' => 'laura.cleven',
                'victim' => 'laura.cleven',
                'open_date' => '2017-04-11',
                'closure_date' => '2017-04-30',
                'expired' => '0',
                'type' => 'incident',
                'security_incident_status_id' => '2',
                'auto_close_incident' => '1',
                'security_incident_classification_id' => NULL,
                'lifecycle_incomplete' => '1',
                'ongoing_incident' => '1',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                'created' => '2017-04-11 13:12:25',
                'modified' => '2017-04-11 13:12:25',
            ],
        ];

        $table = $this->table('security_incidents');
        $table->insert($data)->save();
    }
}
