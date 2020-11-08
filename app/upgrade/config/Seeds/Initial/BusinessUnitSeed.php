<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessUnit seed.
 */
class BusinessUnitSeed extends AbstractSeed
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
                'name' => 'Everyone',
                'description' => '',
                'workflow_status' => '0',
                'workflow_owner_id' => NULL,
                '_hidden' => '1',
                'created' => '2015-12-19 00:00:00',
                'modified' => '2015-12-19 00:00:00',
            ],
        ];

        $table = $this->table('business_units');
        $table->insert($data)->save();
    }
}
