<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityIncidentClassification seed.
 */
class SecurityIncidentClassificationSeed extends AbstractSeed
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
                'name' => 'stolen laptop',
                'created' => '2017-04-11 13:10:29',
            ],
            [
                'id' => '2',
                'security_incident_id' => '1',
                'name' => 'stolen mobile',
                'created' => '2017-04-11 13:10:29',
            ],
        ];

        $table = $this->table('security_incident_classifications');
        $table->insert($data)->save();
    }
}
