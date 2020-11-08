<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityServicesUser seed.
 */
class SecurityServicesUserSeed extends AbstractSeed
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
                'user_id' => '1',
                'created' => '2017-04-10 13:28:37',
            ],
            [
                'id' => '12',
                'security_service_id' => '12',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '13',
                'security_service_id' => '13',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '14',
                'security_service_id' => '14',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '15',
                'security_service_id' => '15',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '16',
                'security_service_id' => '16',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '17',
                'security_service_id' => '17',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '18',
                'security_service_id' => '18',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '19',
                'security_service_id' => '19',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '20',
                'security_service_id' => '20',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '21',
                'security_service_id' => '21',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '22',
                'security_service_id' => '22',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '23',
                'security_service_id' => '23',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:38',
            ],
            [
                'id' => '24',
                'security_service_id' => '24',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:39',
            ],
            [
                'id' => '25',
                'security_service_id' => '25',
                'user_id' => '1',
                'created' => '2017-04-10 13:28:39',
            ],
            [
                'id' => '28',
                'security_service_id' => '5',
                'user_id' => '1',
                'created' => '2017-04-10 14:03:12',
            ],
            [
                'id' => '29',
                'security_service_id' => '6',
                'user_id' => '1',
                'created' => '2017-04-10 14:03:25',
            ],
            [
                'id' => '31',
                'security_service_id' => '11',
                'user_id' => '1',
                'created' => '2017-04-10 14:24:45',
            ],
            [
                'id' => '33',
                'security_service_id' => '1',
                'user_id' => '1',
                'created' => '2017-04-11 12:45:39',
            ],
            [
                'id' => '34',
                'security_service_id' => '3',
                'user_id' => '3',
                'created' => '2017-04-11 12:46:07',
            ],
            [
                'id' => '35',
                'security_service_id' => '3',
                'user_id' => '4',
                'created' => '2017-04-11 12:46:07',
            ],
            [
                'id' => '37',
                'security_service_id' => '4',
                'user_id' => '1',
                'created' => '2017-04-11 12:48:11',
            ],
            [
                'id' => '38',
                'security_service_id' => '7',
                'user_id' => '1',
                'created' => '2017-04-11 12:49:23',
            ],
            [
                'id' => '39',
                'security_service_id' => '8',
                'user_id' => '1',
                'created' => '2017-04-11 12:51:00',
            ],
            [
                'id' => '40',
                'security_service_id' => '9',
                'user_id' => '3',
                'created' => '2017-04-11 12:55:28',
            ],
            [
                'id' => '41',
                'security_service_id' => '26',
                'user_id' => '2',
                'created' => '2017-04-11 13:05:02',
            ],
            [
                'id' => '42',
                'security_service_id' => '2',
                'user_id' => '4',
                'created' => '2017-04-11 16:56:38',
            ],
        ];

        $table = $this->table('security_services_users');
        $table->insert($data)->save();
    }
}
