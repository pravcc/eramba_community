<?php
use Phinx\Seed\AbstractSeed;

/**
 * User seed.
 */
class UserSeed extends AbstractSeed
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
                'name' => 'Admin',
                'surname' => 'Admin',
                'group_id' => '10',
                'email' => 'admin@eramba.org',
                'login' => 'admin',
                'password' => '$2a$10$WhVO3Jj4nFhCj6bToUOztun/oceKY6rT2db2bu430dW5/lU0w9KJ.',
                'language' => 'eng',
                'status' => '1',
                'blocked' => '0',
                'local_account' => '1',
                'api_allow' => '0',
                'created' => '2013-10-14 16:19:04',
                'modified' => '2015-09-11 18:19:52',
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
