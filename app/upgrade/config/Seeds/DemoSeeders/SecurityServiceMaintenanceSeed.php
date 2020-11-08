<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityServiceMaintenance seed.
 */
class SecurityServiceMaintenanceSeed extends AbstractSeed
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
                'security_service_id' => '2',
                'task' => 'Every month a Nessus scan must be executed against core systems (use templates on Nessus). Store this report as is later used for audits.',
                'task_conclusion' => 'Completed, scans attached.',
                'user_id' => '3',
                'planned_date' => '2017-01-22',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-11',
                'result' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:47:44',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '2',
                'security_service_id' => '4',
                'task' => 'Our fire sensor supplier must perform a monthyl report.',
                'task_conclusion' => 'Completed',
                'user_id' => '4',
                'planned_date' => '2017-05-02',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-30',
                'result' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:48:35',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '3',
                'security_service_id' => '5',
                'task' => 'Send a reminder to all employees asking them to ensure they comply with our awareness trainings.',
                'task_conclusion' => '',
                'user_id' => NULL,
                'planned_date' => '2017-03-30',
                'start_date' => NULL,
                'end_date' => NULL,
                'result' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
        ];

        $table = $this->table('security_service_maintenances');
        $table->insert($data)->save();
    }
}
