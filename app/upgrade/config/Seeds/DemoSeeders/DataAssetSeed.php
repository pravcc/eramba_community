<?php
use Phinx\Seed\AbstractSeed;

/**
 * DataAsset seed.
 */
class DataAssetSeed extends AbstractSeed
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
                'description' => 'Something hapens',
                'data_asset_status_id' => '1',
                'asset_id' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-19 07:53:50',
                'modified' => '2017-04-19 07:53:50',
            ],
        ];

        $table = $this->table('data_assets');
        $table->insert($data)->save();
    }
}
