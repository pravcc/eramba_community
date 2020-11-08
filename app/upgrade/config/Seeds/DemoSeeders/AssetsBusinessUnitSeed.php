<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetsBusinessUnit seed.
 */
class AssetsBusinessUnitSeed extends AbstractSeed
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
                'id' => '15',
                'asset_id' => '2',
                'business_unit_id' => '3',
            ],
            [
                'id' => '16',
                'asset_id' => '3',
                'business_unit_id' => '2',
            ],
            [
                'id' => '17',
                'asset_id' => '4',
                'business_unit_id' => '4',
            ],
            [
                'id' => '18',
                'asset_id' => '5',
                'business_unit_id' => '4',
            ],
            [
                'id' => '19',
                'asset_id' => '6',
                'business_unit_id' => '4',
            ],
            [
                'id' => '20',
                'asset_id' => '7',
                'business_unit_id' => '4',
            ],
            [
                'id' => '21',
                'asset_id' => '8',
                'business_unit_id' => '4',
            ],
            [
                'id' => '22',
                'asset_id' => '9',
                'business_unit_id' => '4',
            ],
            [
                'id' => '23',
                'asset_id' => '10',
                'business_unit_id' => '4',
            ],
            [
                'id' => '24',
                'asset_id' => '11',
                'business_unit_id' => '4',
            ],
            [
                'id' => '25',
                'asset_id' => '12',
                'business_unit_id' => '3',
            ],
            [
                'id' => '26',
                'asset_id' => '13',
                'business_unit_id' => '3',
            ],
            [
                'id' => '28',
                'asset_id' => '1',
                'business_unit_id' => '2',
            ],
        ];

        $table = $this->table('assets_business_units');
        $table->insert($data)->save();
    }
}
