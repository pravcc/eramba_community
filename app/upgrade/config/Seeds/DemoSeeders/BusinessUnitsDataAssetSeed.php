<?php
use Phinx\Seed\AbstractSeed;

/**
 * BusinessUnitsDataAsset seed.
 */
class BusinessUnitsDataAssetSeed extends AbstractSeed
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
                'business_unit_id' => '2',
                'data_asset_id' => '1',
            ],
            [
                'id' => '2',
                'business_unit_id' => '3',
                'data_asset_id' => '1',
            ],
        ];

        $table = $this->table('business_units_data_assets');
        $table->insert($data)->save();
    }
}
