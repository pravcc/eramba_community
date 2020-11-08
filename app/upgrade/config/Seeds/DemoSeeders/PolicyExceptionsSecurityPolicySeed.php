<?php
use Phinx\Seed\AbstractSeed;

/**
 * PolicyExceptionsSecurityPolicy seed.
 */
class PolicyExceptionsSecurityPolicySeed extends AbstractSeed
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
                'id' => '3',
                'policy_exception_id' => '1',
                'security_policy_id' => '2',
            ],
            [
                'id' => '4',
                'policy_exception_id' => '1',
                'security_policy_id' => '4',
            ],
            [
                'id' => '5',
                'policy_exception_id' => '2',
                'security_policy_id' => '31',
            ],
            [
                'id' => '6',
                'policy_exception_id' => '2',
                'security_policy_id' => '30',
            ],
        ];

        $table = $this->table('policy_exceptions_security_policies');
        $table->insert($data)->save();
    }
}
