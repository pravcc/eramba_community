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
                'modified' => '2017-04-25 03:19:36',
            ],
            [
                'id' => '2',
                'name' => 'Esteban',
                'surname' => 'Ribicic',
                'group_id' => '10',
                'email' => 'esteban.ribicic@eramba.org',
                'login' => 'esteban.ribicic',
                'password' => '$2a$10$uXK8KCmCY014blhVEEhfKOn9y/3pBfnEGvxdcoGkNtkCtJHh7km5q',
                'language' => 'eng',
                'status' => '1',
                'blocked' => '0',
                'local_account' => '1',
                'api_allow' => '0',
                'created' => '2017-04-10 13:59:26',
                'modified' => '2017-04-10 13:59:26',
            ],
            [
                'id' => '3',
                'name' => 'Goran',
                'surname' => 'Galic',
                'group_id' => '10',
                'email' => 'goran.galic@eramba.org',
                'login' => 'goran.galic',
                'password' => '$2a$10$QMDIaUI3JM5qkSfljx/Kt.3cwsWUcWxPKxX6M80C1qvIJTaGTJxT6',
                'language' => 'eng',
                'status' => '1',
                'blocked' => '0',
                'local_account' => '1',
                'api_allow' => '0',
                'created' => '2017-04-10 13:59:46',
                'modified' => '2017-04-10 13:59:46',
            ],
            [
                'id' => '4',
                'name' => 'Maria',
                'surname' => 'Matovicova',
                'group_id' => '11',
                'email' => 'test@eramba.org',
                'login' => 'maria.matovicova',
                'password' => '$2a$10$cxFtw0ArQRG9NWSvB8J8LekUsIyGQQ7YHHZQANLRDdkiRnGY1GfLK',
                'language' => 'eng',
                'status' => '1',
                'blocked' => '0',
                'local_account' => '1',
                'api_allow' => '0',
                'created' => '2017-04-11 10:38:15',
                'modified' => '2017-04-11 10:38:15',
            ],
            [
                'id' => '5',
                'name' => 'Michelle',
                'surname' => 'Morrison',
                'group_id' => '10',
                'email' => 'dev@eramba.org',
                'login' => 'Michelle.Morrison',
                'password' => '',
                'language' => 'eng',
                'status' => '1',
                'blocked' => '0',
                'local_account' => '0',
                'api_allow' => '0',
                'created' => '2017-04-11 13:42:23',
                'modified' => '2017-04-11 13:42:23',
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
