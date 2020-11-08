<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityPoliciesSecurityService seed.
 */
class SecurityPoliciesSecurityServiceSeed extends AbstractSeed
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
                'security_policy_id' => '6',
                'security_service_id' => '5',
            ],
            [
                'id' => '5',
                'security_policy_id' => '19',
                'security_service_id' => '5',
            ],
            [
                'id' => '6',
                'security_policy_id' => '27',
                'security_service_id' => '5',
            ],
            [
                'id' => '7',
                'security_policy_id' => '13',
                'security_service_id' => '6',
            ],
            [
                'id' => '9',
                'security_policy_id' => '30',
                'security_service_id' => '11',
            ],
            [
                'id' => '10',
                'security_policy_id' => '19',
                'security_service_id' => '11',
            ],
            [
                'id' => '12',
                'security_policy_id' => '40',
                'security_service_id' => '1',
            ],
            [
                'id' => '13',
                'security_policy_id' => '10',
                'security_service_id' => '1',
            ],
            [
                'id' => '14',
                'security_policy_id' => '11',
                'security_service_id' => '1',
            ],
            [
                'id' => '15',
                'security_policy_id' => '16',
                'security_service_id' => '1',
            ],
            [
                'id' => '16',
                'security_policy_id' => '35',
                'security_service_id' => '3',
            ],
            [
                'id' => '19',
                'security_policy_id' => '35',
                'security_service_id' => '4',
            ],
            [
                'id' => '20',
                'security_policy_id' => '35',
                'security_service_id' => '7',
            ],
            [
                'id' => '21',
                'security_policy_id' => '4',
                'security_service_id' => '8',
            ],
            [
                'id' => '22',
                'security_policy_id' => '21',
                'security_service_id' => '8',
            ],
            [
                'id' => '23',
                'security_policy_id' => '39',
                'security_service_id' => '8',
            ],
            [
                'id' => '24',
                'security_policy_id' => '33',
                'security_service_id' => '9',
            ],
            [
                'id' => '25',
                'security_policy_id' => '1',
                'security_service_id' => '26',
            ],
            [
                'id' => '26',
                'security_policy_id' => '42',
                'security_service_id' => '26',
            ],
            [
                'id' => '27',
                'security_policy_id' => '13',
                'security_service_id' => '26',
            ],
            [
                'id' => '28',
                'security_policy_id' => '26',
                'security_service_id' => '2',
            ],
            [
                'id' => '29',
                'security_policy_id' => '28',
                'security_service_id' => '2',
            ],
        ];

        $table = $this->table('security_policies_security_services');
        $table->insert($data)->save();
    }
}
