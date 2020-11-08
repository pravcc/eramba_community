<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartyType seed.
 */
class ThirdPartyTypeSeed extends AbstractSeed
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
                'name' => 'Customers',
            ],
            [
                'id' => '2',
                'name' => 'Suppliers',
            ],
            [
                'id' => '3',
                'name' => 'Regulators',
            ],
        ];

        $table = $this->table('third_party_types');
        $table->insert($data)->save();
    }
}
