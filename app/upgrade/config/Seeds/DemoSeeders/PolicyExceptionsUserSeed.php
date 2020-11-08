<?php
use Phinx\Seed\AbstractSeed;

/**
 * PolicyExceptionsUser seed.
 */
class PolicyExceptionsUserSeed extends AbstractSeed
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
                'policy_exception_id' => '1',
                'user_id' => '2',
                'created' => '2017-04-10 14:44:46',
            ],
            [
                'id' => '3',
                'policy_exception_id' => '2',
                'user_id' => '3',
                'created' => '2017-04-10 14:45:46',
            ],
        ];

        $table = $this->table('policy_exceptions_users');
        $table->insert($data)->save();
    }
}
