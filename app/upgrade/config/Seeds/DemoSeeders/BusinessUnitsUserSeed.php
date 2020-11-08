<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessUnitsUser seed.
 */
class BusinessUnitsUserSeed extends AbstractSeed
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
                'business_unit_id' => '2',
                'user_id' => '1',
                'created' => '2017-04-10 13:38:53',
            ],
            [
                'id' => '5',
                'business_unit_id' => '3',
                'user_id' => '1',
                'created' => '2017-04-10 13:39:15',
            ],
            [
                'id' => '6',
                'business_unit_id' => '4',
                'user_id' => '1',
                'created' => '2017-04-10 13:39:31',
            ],
        ];

        $table = $this->table('business_units_users');
        $table->insert($data)->save();
    }
}
