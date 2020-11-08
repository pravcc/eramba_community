<?php
use Phinx\Seed\AbstractSeed;

/**
 * AwarenessProgramIgnoredUser seed.
 */
class AwarenessProgramIgnoredUserSeed extends AbstractSeed
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
                'id' => '4',
                'awareness_program_id' => '2',
                'uid' => 'Amanda.Davies',
                'created' => '2017-04-11 13:52:54',
            ],
            [
                'id' => '5',
                'awareness_program_id' => '2',
                'uid' => 'Emma.Butler',
                'created' => '2017-04-11 13:52:54',
            ],
            [
                'id' => '6',
                'awareness_program_id' => '3',
                'uid' => 'Christopher.Hardacre',
                'created' => '2017-04-11 15:54:52',
            ],
            [
                'id' => '7',
                'awareness_program_id' => '3',
                'uid' => 'Emma.Butler',
                'created' => '2017-04-11 15:54:52',
            ],
            [
                'id' => '11',
                'awareness_program_id' => '1',
                'uid' => 'Maria.Matovicova',
                'created' => '2017-04-11 16:50:30',
            ],
            [
                'id' => '12',
                'awareness_program_id' => '1',
                'uid' => 'Amanda.Davies',
                'created' => '2017-04-11 16:50:30',
            ],
            [
                'id' => '13',
                'awareness_program_id' => '1',
                'uid' => 'Carol.Gibson',
                'created' => '2017-04-11 16:50:30',
            ],
        ];

        $table = $this->table('awareness_program_ignored_users');
        $table->insert($data)->save();
    }
}
