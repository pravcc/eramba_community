<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityIncidentStatus seed.
 */
class SecurityIncidentStatusSeed extends AbstractSeed
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
                'name' => 'Ongoing',
            ],
            [
                'id' => '3',
                'name' => 'Closed',
            ],
        ];

        $table = $this->table('security_incident_statuses');
        $table->insert($data)->save();
    }
}
