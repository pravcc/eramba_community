<?php
use Phinx\Seed\AbstractSeed;

/**
 * DemoData seed.
 */
class DemoSeed extends AbstractSeed
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
        $file = __DIR__ . DS . 'Demo' . DS . 'demo_data.sql';
        if (!file_exists($file)) {
            throw new \Exception('Demo data seed sql file not found.');
        }
        $sql = file_get_contents($file);
        $this->query($sql);

        // $this->additionalData();
    }

    /* Example only
    public function additionalData() {
        $data = [
            [
                'id' => 6,
                'name' => 'Visualisation',
                'surname' => 'Test',
                'email' => 'visualisation@eramba.org',
                'login' => 'visualisation',
                'password' => '$2a$10$WhVO3Jj4nFhCj6bToUOztun/oceKY6rT2db2bu430dW5/lU0w9KJ.',
                'language' => 'eng',
                'status' => '1',
                'blocked' => '0',
                'local_account' => '1',
                'api_allow' => '0',
                'created' => '2013-10-14 16:19:04',
                'modified' => '2017-04-25 03:19:36',
            ]
        ];

        $table = $this->table('users');
        $table->insert($data)->save();

        $data = [
            [
                'user_id' => 6,
                'group_id' => 13
            ]
        ];

        $table = $this->table('users_groups');
        $table->insert($data)->save();
    }
    */
}
