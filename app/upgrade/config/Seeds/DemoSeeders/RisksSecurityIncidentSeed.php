<?php
use Phinx\Seed\AbstractSeed;

/**
 * RisksSecurityIncident seed.
 */
class RisksSecurityIncidentSeed extends AbstractSeed
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
                'risk_id' => '1',
                'security_incident_id' => '1',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 13:10:29',
            ],
        ];

        $table = $this->table('risks_security_incidents');
        $table->insert($data)->save();
    }
}
