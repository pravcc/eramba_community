<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityIncidentsSecurityService seed.
 */
class SecurityIncidentsSecurityServiceSeed extends AbstractSeed
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
                'security_incident_id' => '1',
                'security_service_id' => '26',
            ],
            [
                'id' => '2',
                'security_incident_id' => '2',
                'security_service_id' => '1',
            ],
        ];

        $table = $this->table('security_incidents_security_services');
        $table->insert($data)->save();
    }
}
