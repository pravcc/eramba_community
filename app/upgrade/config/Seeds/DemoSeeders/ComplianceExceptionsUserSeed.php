<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceExceptionsUser seed.
 */
class ComplianceExceptionsUserSeed extends AbstractSeed
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
                'compliance_exception_id' => '1',
                'user_id' => '2',
                'created' => '2017-04-10 14:41:54',
            ],
        ];

        $table = $this->table('compliance_exceptions_users');
        $table->insert($data)->save();
    }
}
