<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityServiceAuditDate seed.
 */
class SecurityServiceAuditDateSeed extends AbstractSeed
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
                'security_service_id' => '10',
                'day' => '19',
                'month' => '6',
            ],
            [
                'id' => '12',
                'security_service_id' => '12',
                'day' => '4',
                'month' => '9',
            ],
            [
                'id' => '13',
                'security_service_id' => '13',
                'day' => '4',
                'month' => '9',
            ],
            [
                'id' => '14',
                'security_service_id' => '14',
                'day' => '19',
                'month' => '3',
            ],
            [
                'id' => '15',
                'security_service_id' => '15',
                'day' => '2',
                'month' => '11',
            ],
            [
                'id' => '16',
                'security_service_id' => '16',
                'day' => '20',
                'month' => '1',
            ],
            [
                'id' => '17',
                'security_service_id' => '17',
                'day' => '4',
                'month' => '2',
            ],
            [
                'id' => '18',
                'security_service_id' => '18',
                'day' => '14',
                'month' => '10',
            ],
            [
                'id' => '19',
                'security_service_id' => '19',
                'day' => '19',
                'month' => '3',
            ],
            [
                'id' => '20',
                'security_service_id' => '20',
                'day' => '1',
                'month' => '1',
            ],
            [
                'id' => '21',
                'security_service_id' => '21',
                'day' => '16',
                'month' => '6',
            ],
            [
                'id' => '22',
                'security_service_id' => '22',
                'day' => '5',
                'month' => '3',
            ],
            [
                'id' => '23',
                'security_service_id' => '23',
                'day' => '28',
                'month' => '1',
            ],
            [
                'id' => '24',
                'security_service_id' => '24',
                'day' => '10',
                'month' => '10',
            ],
            [
                'id' => '25',
                'security_service_id' => '25',
                'day' => '15',
                'month' => '4',
            ],
            [
                'id' => '28',
                'security_service_id' => '5',
                'day' => '30',
                'month' => '6',
            ],
            [
                'id' => '29',
                'security_service_id' => '6',
                'day' => '4',
                'month' => '10',
            ],
            [
                'id' => '31',
                'security_service_id' => '11',
                'day' => '24',
                'month' => '10',
            ],
            [
                'id' => '34',
                'security_service_id' => '1',
                'day' => '15',
                'month' => '3',
            ],
            [
                'id' => '35',
                'security_service_id' => '1',
                'day' => '11',
                'month' => '2',
            ],
            [
                'id' => '36',
                'security_service_id' => '3',
                'day' => '4',
                'month' => '1',
            ],
            [
                'id' => '39',
                'security_service_id' => '4',
                'day' => '26',
                'month' => '1',
            ],
            [
                'id' => '40',
                'security_service_id' => '4',
                'day' => '11',
                'month' => '10',
            ],
            [
                'id' => '41',
                'security_service_id' => '7',
                'day' => '5',
                'month' => '2',
            ],
            [
                'id' => '42',
                'security_service_id' => '8',
                'day' => '13',
                'month' => '3',
            ],
            [
                'id' => '43',
                'security_service_id' => '8',
                'day' => '11',
                'month' => '11',
            ],
            [
                'id' => '44',
                'security_service_id' => '9',
                'day' => '11',
                'month' => '4',
            ],
            [
                'id' => '45',
                'security_service_id' => '26',
                'day' => '11',
                'month' => '9',
            ],
            [
                'id' => '46',
                'security_service_id' => '2',
                'day' => '2',
                'month' => '1',
            ],
            [
                'id' => '47',
                'security_service_id' => '2',
                'day' => '11',
                'month' => '10',
            ],
        ];

        $table = $this->table('security_service_audit_dates');
        $table->insert($data)->save();
    }
}
