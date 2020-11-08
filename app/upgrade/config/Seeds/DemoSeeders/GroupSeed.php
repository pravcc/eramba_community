<?php
use Phinx\Seed\AbstractSeed;

/**
 * Group seed.
 */
class GroupSeed extends AbstractSeed
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
                'id' => '10',
                'name' => 'Admin',
                'description' => '',
                'status' => '1',
                'created' => '2013-10-14 16:18:08',
                'modified' => '2013-10-14 16:18:08',
            ],
            [
                'id' => '11',
                'name' => 'Third Party Feedback',
                'description' => '',
                'status' => '1',
                'created' => '2016-01-07 17:07:53',
                'modified' => '2016-01-07 17:07:53',
            ],
            [
                'id' => '12',
                'name' => 'Notification Feedback',
                'description' => '',
                'status' => '1',
                'created' => '2016-01-07 17:08:02',
                'modified' => '2016-01-07 17:08:02',
            ],
            [
                'id' => '13',
                'name' => 'All but Settings',
                'description' => '',
                'status' => '1',
                'created' => '2016-01-07 17:08:10',
                'modified' => '2016-01-07 17:08:10',
            ],
        ];

        $table = $this->table('groups');
        $table->insert($data)->save();
    }
}
