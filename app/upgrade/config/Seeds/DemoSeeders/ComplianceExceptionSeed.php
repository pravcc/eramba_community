<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceException seed.
 */
class ComplianceExceptionSeed extends AbstractSeed
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
                'title' => 'Disciplinary Process',
                'description' => 'Our organisation does not have a written disciplinary process as its not aligned with the culture of our business.',
                'expiration' => '2017-08-24',
                'expired' => '0',
                'status' => '1',
                'created' => '2017-04-10 14:41:54',
                'modified' => '2017-04-10 14:41:54',
            ],
        ];

        $table = $this->table('compliance_exceptions');
        $table->insert($data)->save();
    }
}
