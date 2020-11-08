<?php
use Phinx\Seed\AbstractSeed;

/**
 * AwarenessProgramLdapGroup seed.
 */
class AwarenessProgramLdapGroupSeed extends AbstractSeed
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
                'awareness_program_id' => '2',
                'name' => 'Network_Team',
                'created' => '2017-04-11 13:52:54',
            ],
            [
                'id' => '3',
                'awareness_program_id' => '3',
                'name' => 'Network_Team',
                'created' => '2017-04-11 15:54:52',
            ],
            [
                'id' => '5',
                'awareness_program_id' => '1',
                'name' => 'Network_Team',
                'created' => '2017-04-11 16:50:30',
            ],
        ];

        $table = $this->table('awareness_program_ldap_groups');
        $table->insert($data)->save();
    }
}
