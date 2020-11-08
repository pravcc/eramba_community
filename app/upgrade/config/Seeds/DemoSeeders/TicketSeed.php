<?php
use Phinx\Seed\AbstractSeed;

/**
 * Ticket seed.
 */
class TicketSeed extends AbstractSeed
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
                'is_used' => '0',
                'hash' => '0393a9e0063183258d394a75e516b46a37e47505',
                'data' => 'esteban.ribicic@eramba.org',
                'created' => '2017-04-11 10:06:24',
                'modified' => '2017-04-11 10:06:24',
                'expires' => '2017-04-12 10:06:24',
            ],
            [
                'id' => '2',
                'is_used' => '0',
                'hash' => '4e9f44a40e6ab48703a42d3375dafe6a41341f92',
                'data' => 'esteban.ribicic@eramba.org',
                'created' => '2017-04-11 10:07:57',
                'modified' => '2017-04-11 10:07:57',
                'expires' => '2017-04-12 10:07:57',
            ],
        ];

        $table = $this->table('tickets');
        $table->insert($data)->save();
    }
}
