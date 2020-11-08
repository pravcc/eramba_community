<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartiesUser seed.
 */
class ThirdPartiesUserSeed extends AbstractSeed
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
                'third_party_id' => '2',
                'user_id' => '1',
                'created' => '2017-04-10 13:47:32',
            ],
            [
                'id' => '2',
                'third_party_id' => '3',
                'user_id' => '1',
                'created' => '2017-04-10 13:51:05',
            ],
            [
                'id' => '3',
                'third_party_id' => '4',
                'user_id' => '1',
                'created' => '2017-04-10 13:55:26',
            ],
            [
                'id' => '4',
                'third_party_id' => '5',
                'user_id' => '1',
                'created' => '2017-04-10 15:57:55',
            ],
            [
                'id' => '5',
                'third_party_id' => '6',
                'user_id' => '3',
                'created' => '2017-04-11 13:22:53',
            ],
            [
                'id' => '6',
                'third_party_id' => '7',
                'user_id' => '3',
                'created' => '2017-04-12 07:58:34',
            ],
        ];

        $table = $this->table('third_parties_users');
        $table->insert($data)->save();
    }
}
