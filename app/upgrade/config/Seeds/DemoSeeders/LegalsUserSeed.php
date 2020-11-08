<?php
use Phinx\Seed\AbstractSeed;

/**
 * LegalsUser seed.
 */
class LegalsUserSeed extends AbstractSeed
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
                'legal_id' => '1',
                'user_id' => '2',
                'created' => '2017-04-10 14:56:15',
            ],
            [
                'id' => '2',
                'legal_id' => '2',
                'user_id' => '3',
                'created' => '2017-04-10 14:56:28',
            ],
        ];

        $table = $this->table('legals_users');
        $table->insert($data)->save();
    }
}
