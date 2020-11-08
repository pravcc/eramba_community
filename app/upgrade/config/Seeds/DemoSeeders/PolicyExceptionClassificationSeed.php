<?php
use Phinx\Seed\AbstractSeed;

/**
 * PolicyExceptionClassification seed.
 */
class PolicyExceptionClassificationSeed extends AbstractSeed
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
                'policy_exception_id' => '2',
                'name' => 'DMZ',
                'created' => '2017-04-10 14:45:46',
            ],
            [
                'id' => '2',
                'policy_exception_id' => '2',
                'name' => 'Network',
                'created' => '2017-04-10 14:45:46',
            ],
        ];

        $table = $this->table('policy_exception_classifications');
        $table->insert($data)->save();
    }
}
