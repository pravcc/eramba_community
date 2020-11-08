<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetsSecurityIncident seed.
 */
class AssetsSecurityIncidentSeed extends AbstractSeed
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
                'asset_id' => '10',
                'security_incident_id' => '1',
                'created' => '2017-04-11 13:10:29',
            ],
            [
                'id' => '2',
                'asset_id' => '7',
                'security_incident_id' => '1',
                'created' => '2017-04-11 13:10:29',
            ],
            [
                'id' => '3',
                'asset_id' => '1',
                'security_incident_id' => '2',
                'created' => '2017-04-11 13:12:25',
            ],
            [
                'id' => '4',
                'asset_id' => '3',
                'security_incident_id' => '2',
                'created' => '2017-04-11 13:12:25',
            ],
        ];

        $table = $this->table('assets_security_incidents');
        $table->insert($data)->save();
    }
}
