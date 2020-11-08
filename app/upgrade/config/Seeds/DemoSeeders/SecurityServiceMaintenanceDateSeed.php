<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityServiceMaintenanceDate seed.
 */
class SecurityServiceMaintenanceDateSeed extends AbstractSeed
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
                'id' => '5',
                'security_service_id' => '5',
                'day' => '30',
                'month' => '3',
            ],
            [
                'id' => '7',
                'security_service_id' => '2',
                'day' => '22',
                'month' => '1',
            ],
        ];

        $table = $this->table('security_service_maintenance_dates');
        $table->insert($data)->save();
    }
}
