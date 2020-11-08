<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskException seed.
 */
class RiskExceptionSeed extends AbstractSeed
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
                'title' => 'Lack of IDS',
                'description' => 'We need to implement IDS systems on our network.',
                'author_id' => '3',
                'expiration' => '2017-04-30',
                'expired' => '0',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:25:08',
                'modified' => '2017-04-11 13:25:08',
            ],
        ];

        $table = $this->table('risk_exceptions');
        $table->insert($data)->save();
    }
}
