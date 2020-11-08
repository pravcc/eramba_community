<?php
use Phinx\Seed\AbstractSeed;

/**
 * RisksSecurityService seed.
 */
class RisksSecurityServiceSeed extends AbstractSeed
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
                'risk_id' => '2',
                'security_service_id' => '1',
            ],
            [
                'id' => '3',
                'risk_id' => '2',
                'security_service_id' => '20',
            ],
            [
                'id' => '4',
                'risk_id' => '2',
                'security_service_id' => '19',
            ],
            [
                'id' => '5',
                'risk_id' => '2',
                'security_service_id' => '14',
            ],
            [
                'id' => '6',
                'risk_id' => '2',
                'security_service_id' => '21',
            ],
            [
                'id' => '7',
                'risk_id' => '1',
                'security_service_id' => '26',
            ],
        ];

        $table = $this->table('risks_security_services');
        $table->insert($data)->save();
    }
}
