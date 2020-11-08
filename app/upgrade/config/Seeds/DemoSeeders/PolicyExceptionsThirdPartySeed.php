<?php
use Phinx\Seed\AbstractSeed;

/**
 * PolicyExceptionsThirdParty seed.
 */
class PolicyExceptionsThirdPartySeed extends AbstractSeed
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
                'policy_exception_id' => '1',
                'third_party_id' => '2',
            ],
        ];

        $table = $this->table('policy_exceptions_third_parties');
        $table->insert($data)->save();
    }
}
