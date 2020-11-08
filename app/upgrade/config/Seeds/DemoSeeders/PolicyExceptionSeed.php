<?php
use Phinx\Seed\AbstractSeed;

/**
 * PolicyException seed.
 */
class PolicyExceptionSeed extends AbstractSeed
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
                'title' => 'Access to Production DB',
                'description' => 'Ticket:567832

We need to provide access to applications teams to databases in order for them to be able to debug.',
                'expiration' => '2017-07-31',
                'expired' => '0',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 14:43:58',
                'modified' => '2017-04-10 15:37:12',
            ],
            [
                'id' => '2',
                'title' => 'SSH DMZ Access',
                'description' => 'We need to open the firewall for SSH to the internet public servers so contractors can debug issues with an application:

Ticket: F75377',
                'expiration' => '2018-01-31',
                'expired' => '0',
                'status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 14:45:46',
                'modified' => '2017-04-10 14:45:46',
            ],
        ];

        $table = $this->table('policy_exceptions');
        $table->insert($data)->save();
    }
}
