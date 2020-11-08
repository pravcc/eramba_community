<?php
use Phinx\Seed\AbstractSeed;

/**
 * DataAssetsThirdParty seed.
 */
class DataAssetsThirdPartySeed extends AbstractSeed
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
                'data_asset_id' => '1',
                'third_party_id' => '3',
            ],
        ];

        $table = $this->table('data_assets_third_parties');
        $table->insert($data)->save();
    }
}
